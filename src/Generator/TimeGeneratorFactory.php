<?php
/**
 * This file is part of the ramsey/uuid library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Copyright (c) Ben Ramsey <ben@benramsey.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link https://benramsey.com/projects/ramsey-uuid/ Documentation
 * @link https://packagist.org/packages/ramsey/uuid Packagist
 * @link https://github.com/ramsey/uuid GitHub
 */

namespace Ramsey\Uuid\Generator;

use Ramsey\Uuid\FeatureSet;

class TimeGeneratorFactory
{
    /**
     * @var FeatureSet
     */
    private $featureSet;

    public function __construct(FeatureSet $featureSet)
    {
        $this->featureSet = $featureSet;
    }

    public function getGenerator()
    {
        return new DefaultTimeGenerator(
            $this->featureSet->getNodeProvider(),
            $this->featureSet->getTimeConverter(),
            $this->featureSet->getTimeProvider()
        );
    }
}
