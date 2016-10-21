<?php
namespace SiteMaster\Core\Auditor\Logger;

use DOMXPath;
use Monolog\Logger;
use SiteMaster\Core\Registry\Site;
use SiteMaster\Core\Auditor\Scan;
use SiteMaster\Core\Auditor\Site\Page;
use SiteMaster\Core\Util;

class Metrics extends \Spider_LoggerAbstract
{
    /**
     * @var bool|\Spider
     */
    protected $spider = false;

    /**
     * @var bool|Scan
     */
    protected $scan = false;

    /**
     * @var bool|Site
     */
    protected $site = false;

    /**
     * @var bool|Page
     */
    protected $page = false;

    /**
     * 
     * @var false|array
     */
    protected $phantomjs_results = false;

    function __construct(\Spider $spider, Scan $scan, Site $site, Page $page, $phantomjs_results)
    {
        $this->spider = $spider;
        $this->scan = $scan;
        $this->site = $site;
        $this->page = $page;
        $this->phantomjs_results = $phantomjs_results;
    }

    public function log($uri, $depth, DOMXPath $xpath)
    {
        $metrics = new \SiteMaster\Core\Auditor\Metrics();
        
        foreach ($metrics as $metric) {
            /**
             * @var \SiteMaster\Core\Auditor\MetricInterface $metric
             */

            $metric_phantom_results = false;
            
            $plugin = $metric->getPlugin();
            
            if (isset($this->phantomjs_results[$plugin->getMachineName()])) {
                $metric_phantom_results = $this->phantomjs_results[$plugin->getMachineName()];
                if (isset($metricPhantomResults['exception'])) {
                    Util::log(Logger::ERROR, 'phantomjs metric exception', array(
                        'result' => $metricPhantomResults,
                    ));
                }
            }
            
            $metric->performScan($uri, $xpath, $depth, $this->page, $this, $metric_phantom_results);
        }
    }

    /**
     * @return bool|\Spider
     */
    public function getSpider()
    {
        return $this->spider;
    }

    /**
     * @return bool|Scan
     */
    public function getScan()
    {
        return $this->scan;
    }

    /**
     * @return bool|Site
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * @return bool|Page
     */
    public function getPage()
    {
        return $this->page;
    }
}
