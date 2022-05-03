<?php

namespace App\Services;

use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\MultipartStream;
use Lcobucci\JWT\Parser as JwtParser;
use Auth;
use Log;

class APIService
{
    const ERROR_SOURCE_REQUEST_API = 'error-request-api';

    const ERROR_SOURCE_INTERNAL = 'error-internal';

    const MULTIPART_BOUNDARY = 'X-APISERVICE-BOUNDARY';

    protected $_guzzle_client;

    public function __construct(Client $guzzleClient)
    {
        $this->_guzzle_client = $guzzleClient;
    }

    /**
     * Request JWT Token
     *
     * @param int $memberId
     * @param string $password
     *
     * @return array
     */
    public function requestJWTToken($user_id, $user_password)
    {
        $uri = "/token/request/";
        $contentType = 'multipart/form-data';
        $body = [
            'type' => 'body',
            'boundary' => true,
            'data' => [
                'username_jwt' => env('USERNAME_JWT'),
                'password_jwt' => env('PASSWORD_JWT'),
                'user_id' => $user_id,
                'password' => $user_password
            ]
        ];

        // Initialize headers
        $headers = [
            'ip' => '',
            'origin' => env('ORIGIN'),
            'source' => env('SOURCE')
        ];

        $response = $this->requestApi(env('API_SERVER'), 'POST', $uri, $contentType, $headers, $body);

        return $this->parseResponseToken($response);
    }

    public function requestApi($host, $method, $uri, $contentType = '', $headers = [], $body = [], $requiredToken = false)
    {
        $actual_error = '';
        $status_code = 500;
        $error_source = self::ERROR_SOURCE_INTERNAL;
        $options = [];

        try {
            // Initialize body
            if (!empty($body)) {
                $body_type = $body['type'];
                if (isset($body['boundary']) && $body['boundary'] && $body_type === 'body') {
                    $multipart_data = [];
                    foreach ($body['data'] as $key => $value) {
                        $multipart_data[] = [
                            'name' => $key,
                            'contents' => $value
                        ];
                    }

                    $contentType .= '; boundary=' . self::MULTIPART_BOUNDARY;
                    $options[$body_type] = new MultipartStream($multipart_data, self::MULTIPART_BOUNDARY);
                } else {
                    $options[$body_type] = $body['data'];
                }
            }

            if (!empty($contentType)) {
                $headers['Content-Type'] = $contentType;
            }

            if ($requiredToken) {
                $token = $this->getToken();

                $headers['Authorization'] = "Bearer {$token}";
            }

            // set header
            $options['headers'] = $headers;

            // set timeout yang telah di konfigurasi di .env
            $options['timeout'] = env('TIMEOUT');

            Log::info("[API-SERVICE] Request to " . $host . $uri);

            // melakukan request ke API
            $response = $this->_guzzle_client->request($method, $host . $uri, $options);

            // response code dari API
            if ($response->getStatusCode() === 200) {
                $response_data = json_decode($response->getBody()->getContents(), true);

                $result = [
                    'data' => $response_data,
                    'status-code' => 200
                ];

                $parse_response = $this->parseResponse($result);

                return $parse_response;
            } else {
                $actual_error = $response->getReasonPhrase();
                $status_code = $response->getStatusCode();
                $error_source = self::ERROR_SOURCE_REQUEST_API;
            }
        } catch (ConnectException $e) {
            $actual_error = $e->getMessage();
            $error_source = self::ERROR_SOURCE_REQUEST_API;

            Log::info("[API-SERVICE] ACTUAL ERROR: " . $actual_error);
            Log::info("[API-SERVICE] ERROR SOURCE: " . $error_source);
        } catch (GuzzleException $e) {
            if ($e->hasResponse()) {
                $e_response = $e->getResponse();
                $actual_error = $e_response->getReasonPhrase();
                $status_code = $e_response->getStatusCode();
                $error_source = self::ERROR_SOURCE_REQUEST_API;

                Log::info("[API-SERVICE] ACTUAL ERROR: " . $actual_error);
                Log::info("[API-SERVICE] STATUS CODE: " . $status_code);
                Log::info("[API-SERVICE] ERROR SOURCE: " . $error_source);
            }
        }

        return [
            'message' => '',
            'error' => $actual_error,
            'error-source' => $error_source,
            'status-code' => $status_code,
        ];
    }

    public function getToken()
    {
        if (request()->session()->has('user.pass')) {
            if (Auth::user()) {
                $is_required_new_token = false;

                // cek dari session jwt yang di simpan
                if (request()->session()->has('jwt.token')) {
                    $jwt_parser = new JwtParser;

                    // ambil jwt token dari session
                    $jwt_token = session('jwt.token');

                    // parse jwt token
                    $parsed_token = $jwt_parser->parse($jwt_token);

                    // masukin time expired cek expired time yang telah di konfigurasi pada .env dari jwt token
                    $request_earlier_in = env('EXPIRED_TIME');
                    $jwt_time_prompt = Carbon::now()->addSeconds($request_earlier_in);

                    $is_required_new_token = $parsed_token->isExpired($jwt_time_prompt);
                } else {
                    $is_required_new_token = true;
                }

                // jika membutukan token baru
                if ($is_required_new_token) {
                    // ambil user id yang sedang login dan password
                    $user_id = Auth::user()->id;
                    $user_password = session("user.pass");

                    Log::info("[API-SERVICE] Request JWT Token");

                    // request jwt token
                    $results = $this->requestJWTToken($user_id, $user_password);

                    // jika response dari request token berhasil
                    if ($results['is_success']) {
                        // hapus session jwt error dan masukan jwt token yang baru
                        request()->session()->forget('jwt.error');
                        session(["jwt.token" => $results['data']['data']['token']]);

                        Log::info("[API-SERVICE] JWT Token: " . $results['data']['data']['token']);
                    } else {
                        session(["jwt.error" => $results['error']]);

                        Log::info("[API-SERVICE] Error Request JWT Token");
                    }
                }

                return session("jwt.token");
            } else {
                return "";
            }
        } else {
            return "";
        }
    }

    protected function parseResponse(array $response)
    {
        $is_success = false;
        $error_message = '';

        if ($response['status-code'] === 200) {
            $status = $response['data']['status'];
            if ($status['id'] === 0) {
                $error_message = $status['message'];
            } else {
                $is_success = true;
            }
        } else {
            $error_message = $response['message'];
        }

        if ($is_success) {
            if (isset($response["data"]) == false) {

                $dummy = Array();
                $dummy["data"]["dummy"] = "";
                if (isset($dummy["status"]) == false) {
                    $dummy["data"] = Array();
                    $dummy["status"]["id"] = 0;
                    $dummy["status"]["message"] = "Lost communication with server, please try again later.";
                }

                $response['data'] = $dummy;
            }

            $result = [
                'is_success' => $is_success,
                'data' => $response['data']
            ];

            Log::info("[API-SERVICE] Parse Response");
            Log::info($result);

            return $result;
        } else {
            if (isset($response["data"]) == false) {

                $dummy = Array();
                if (isset($dummy["status"]) == false) {
                    $dummy["data"] = Array();
                    $dummy["status"]["id"] = 0;
                    $dummy["status"]["message"] = "Lost communication with server, please try again later.";
                }

                $response['data'] = $dummy;
            }

            $result = [
                'is_success' => $is_success,
                'data' => $response['data']
            ];

            Log::info("[API-SERVICE] Parse Response");
            Log::info($result);

            return $result;
        }
    }

    protected function parseResponseToken(array $response)
    {
        $is_success = false;
        $error_message = '';

        if ($response['status-code'] === 200) {
            $status = $response['data']['status'];
            if ($status['id'] === 0) {
                $error_message = $status['message'];
            } else {
                $is_success = true;
            }
        } else {
            $error_message = $response['message'];
        }

        if ($is_success) {
            $result = [
                'is_success' => $is_success,
                'data' => $response['data']
            ];

            Log::info("[API-SERVICE] Parse Response Token");
            Log::info($result);

            return $result;
        } else {
            $result = [
                'is_success' => $is_success,
                'error' => "Lost communication with server, please try again later."
            ];

            Log::info("[API-SERVICE] Error Parse Response Token");
            Log::info($result);

            return $result;
        }
    }
}