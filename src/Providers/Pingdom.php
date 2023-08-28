<?php

namespace WPD\UptimePage\Providers;

use WPD\UptimePage\Exceptions\ProviderException;
use WPD\UptimePage\HttpClient;

class Pingdom implements Provider {

	/**
	 * @var string
	 */
	protected string $token;
	/**
	 * @var string
	 */
	protected string $project;
	/**
	 * @var HttpClient
	 */
	protected HttpClient $client;

	/**
	 * @param string     $token
	 * @param string     $project
	 * @param HttpClient $client
	 */
	public function __construct(
		string $token,
		string $project,
		HttpClient $client
	) {
		$this->token   = $token;
		$this->project = $project;
		$this->client  = $client;
	}

	/**
	 * @return array
	 * @throws ProviderException If the request fails.
	 */
	public function checks(): array {
		$path = 'checks';

		try {
			return $this->client->get(
				$path,
				[],
				[
					'headers' => [
						'Authorization' => "Bearer $this->token",
					],
				]
			);
		} catch ( \Exception $exception ) {
			throw new ProviderException( $path, 0, $exception );
		}
	}

	/**
	 * @return string
	 * @throws ProviderException If the request fails.
	 */
	public function path(): string {
		$response = $this->checks();

		foreach ( $response['checks'] as $check ) {
			if ( $check['name'] === $this->project ) {
				return $check['id'];
			}
		}

		return '';
	}
}
