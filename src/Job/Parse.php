<?php

namespace SpaceSpell\LaravelCrawler\Job;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

use SpaceSpell\LaravelCrawler\ParserFactory;

class Parse implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $scope;

    protected $response;

    protected $context;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($scope, $response, $context = [])
    {
        $this->scope = $scope;
        $this->response = $response;
        $this->context = $context;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $parser = ParserFactory::make($this->scope);
            $parser->setContext($this->context);
            $parser->parse($this->response);
        } catch (\Exception $e) {
            Log::error("Parse job failed", [
                'message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
