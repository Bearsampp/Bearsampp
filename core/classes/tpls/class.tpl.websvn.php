<?php

class TplWebsvn
{
    private function __construct()
    {
    }

    public static function process()
    {
        global $bearsamppBs, $bearsamppBins, $bearsamppApps;

        $result = '<?php' . PHP_EOL . PHP_EOL;

        // Add repository
        foreach ($bearsamppBins->getSvn()->findRepos() as $repo) {
            $result .= '$config->addRepository(\'' . $repo . '\', \'' . Util::formatUnixPath($repo) . '\');' . PHP_EOL;
        }

        // Parent path
        $result .= PHP_EOL . '$config->parentPath(\'' . $bearsamppBins->getSvn()->getRoot() . '\');' . PHP_EOL . PHP_EOL;

        // Templates
        $result .= '$config->addTemplatePath($locwebsvnreal.\'/templates/calm/\');' . PHP_EOL;
        $result .= '$config->addTemplatePath($locwebsvnreal.\'/templates/BlueGrey/\');' . PHP_EOL;
        $result .= '$config->addTemplatePath($locwebsvnreal.\'/templates/Elegant/\');' . PHP_EOL . PHP_EOL;

        // Inline mimetype
        $result .= '$config->addInlineMimeType(\'text/plain\');' . PHP_EOL . PHP_EOL;

        // Min download level
        $result .= '$config->setMinDownloadLevel(2);' . PHP_EOL . PHP_EOL;

        // Geshi
        $result .= '$config->useGeshi();' . PHP_EOL . PHP_EOL;

        // Caching
        $result .= '$config->setRssCachingEnabled(false);' . PHP_EOL . PHP_EOL;

        // Time limit
        $result .= 'set_time_limit(0);' . PHP_EOL . PHP_EOL;

        // Expand tabs by
        $result .= '$config->expandTabsBy(8);' . PHP_EOL . PHP_EOL;

        // Temp dir
        $result .= '$config->setTempDir(\'' . $bearsamppBs->getTmpPath() . '\');' . PHP_EOL . PHP_EOL;

        file_put_contents($bearsamppApps->getWebsvn()->getConf(), $result);
    }
}
