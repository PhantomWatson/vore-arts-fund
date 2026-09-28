<?php

namespace App\Model\Entity;

/**
 * @property \App\Model\Entity\Report $report
 */
class ReportImage extends Image
{
    protected array $_accessible = [
        'report_id' => true,
        'filename' => true,
        'weight' => true,
        'caption' => true,
        'created' => true,
        'report' => true,
    ];
}
