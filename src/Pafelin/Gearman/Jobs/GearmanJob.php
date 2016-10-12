<?php namespace Pafelin\Gearman\Jobs;

use Illuminate\Container\Container;
use Illuminate\Contracts\Queue\Job as QueueJobInterface;
use Illuminate\Queue\Jobs\Job;
use GearmanWorker;
use Exception;

class GearmanJob extends Job implements QueueJobInterface {

    protected $worker;

    protected $job;

    protected $rawPayload = '';

    private $maxRunTime = 1;

    private $single = false;

    public function __construct(Container $container, GearmanWorker $worker, $queue)
    {
        $this->container = $container;
        $this->worker = $worker;
        $this->worker->addFunction($queue, array($this, 'onGearmanJob'));
    }

    public function fire(){

        while($this->worker->work()) {

        }
    }

    public function delete(){
	    parent::delete();
    }

    public function release($delay = 0) {
	    if ($delay > 0) {
		    throw new Exception('No delay is suported');
	    }
    }

    public function attempts() {
        return 1;
    }

    public function getJobId() {
        return base64_encode($this->job);
    }

    public function getContainer() {
        return $this->container;
    }

    public function getGearmanWorker() {
        return $this->worker;
    }

    public function onGearmanJob(\GearmanJob $job) {

        $this->rawPayload = $job->workload();
        parent::fire();
    }

    /**
     * Get the raw body string for the job.
     *
     * @return string
     */
    public function getRawBody() {
        return $this->rawPayload;
    }
}
