<?php

namespace SpaceSpell\LaravelCrawler\Job;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use SpaceSpell\LaravelCrawler\Exception\InvalidJobData;
use SpaceSpell\LaravelCrawler\Scope\ScopeInterface;

class Crawl implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $scope;

    protected $url;

    protected $context;

    protected $options;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($scope, $url, array $context = [], $options = [])
    {
        $this->scope = $scope;
        $this->url = $url;
        $this->context = $context;
        $this->options = $options;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Initialize scope from name.
        $scopeClass = '\\'.$this->scope;

        $scope = new $scopeClass;
        if (!($scope instanceof ScopeInterface)) {
            throw new InvalidJobData('Job scope '.$this->scope.' is not an instance of SpaceSpell\LaravelCrawler\Scope\ScopeInterface');
        }

        $client = new Client([
            'timeout'  => $scope->timeout(),
        ]);

        try {
            $httpOptions = [
                'http_errors' => true,
                "headers" => $scope->headers(),
            ];

            if (isset($this->options['form_params'])) {
                $httpOptions['form_params'] = $this->options['form_params'];
            }

            $response = $client->request($scope->method(), $this->url, $httpOptions);

            $response = mb_convert_encoding((string) $response->getBody(), 'UTF-8', 'UTF-8');

            $parseJob = new Parse([
                "response" => $response,
                "context" => array_merge($this->context, [
                    "url" => $this->url,
                    "crawl_job_id" => $this->job->getJobId(),
                ]),
            ]);

            dispatch($parseJob->onConnection("laravelcrawler")->onQueue($scope->parseQueueName()));

        } catch (\Exception $e) {
            Log::error("Crawl job failed", [
                'url' => $this->url,
                'message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
