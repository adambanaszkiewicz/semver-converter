<?php
/**
 * This file is part of the Atline templating system package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Copyright (c) 2015 - 2016 by Adam Banaszkiewicz
 *
 * @license   MIT License
 * @copyright Copyright (c) 2015 - 2016, Adam Banaszkiewicz
 * @link      https://github.com/requtize/atline
 */

namespace Requtize\SemVerConverter;

use Composer\Semver\VersionParser;
use Composer\Semver\Constraint\Constraint;
use Composer\Semver\Constraint\MultiConstraint;

/**
 * Converts SemVer version (like Composer packages) into integer version with operators.
 * Helps managing versions: store, compare, sort and retrive by conditions.
 *
 * @author Adam Banaszkiewicz https://github.com/requtize
 */
class SemVerConverter
{
    /**
     * How many zeros need to pad?
     * @var integer
     */
    protected $zeros = 3;

    /**
     * How many sections want to generate?
     * Composer SemVer generates 4 by default.
     * @var integer
     */
    protected $sections = 3;

    public function __construct($zeros = 3, $sections = 3)
    {
        $this->zeros    = $zeros;
        $this->sections = $sections;
    }

    public function convert($version)
    {
        $result = [];

        $baseConstraint = (new VersionParser)->parseConstraints($version);

        if($baseConstraint instanceof MultiConstraint)
        {
            $hasOr = strpos($baseConstraint->__toString(), '||') !== false;

            $constraints = $baseConstraint->getConstraints();

            if($hasOr == false && count($constraints) == 2)
            {
                $result[] = $this->convertFromConstraints($constraints[0], $constraints[1]);
            }
            else
            {
                foreach($constraints as $subConstraint)
                {
                    if($subConstraint instanceof MultiConstraint)
                    {
                        $result[] = $this->convertFromConstraints($subConstraint->getConstraints()[0], $subConstraint->getConstraints()[1]);
                    }
                    else
                    {
                        $result[] = $this->convertFromConstraints($subConstraint);
                    }
                }
            }
        }
        else
        {
            $result = [ $this->convertFromConstraints($baseConstraint) ];
        }

        return $result;
    }

    public function convertVersion(Constraint $version)
    {
        // Remove operator
        list($operator, $version) = explode(' ', $version);

        // Remove stability
        list($version) = explode('-', $version);

        // Explode every section
        $sections = explode('.', $version);

        // If there is more sections that we need, we remove last ones
        // to shorts array.
        if(count($sections) > $this->sections)
        {
            list($sections) = array_chunk($sections, $this->sections);
        }
        // Otherwise we add as many as we need.
        else
        {
            while(count($sections) < $this->sections)
            {
                $sections[] = 0;
            }
        }

        // Pad every section with zeros
        $sections = array_map(function ($val) {
            // If any section is longer than padding zeros, we remove last numbers.
            $val = strlen($val) > $this->zeros ? substr($val, 0, $this->zeros) : $val;

            return str_pad($val, $this->zeros, 0);
        }, $sections);

        $result = '';

        foreach($sections as $section)
        {
            $result .= $section;
        }

        return [ (int) $result, $operator ];
    }

    public function convertFromConstraints(Constraint $from, Constraint $to = null)
    {
        $from = $this->convertVersion($from);

        if($to != null)
        {
            $to = $this->convertVersion($to);
        }
        else
        {
            if($from[1] == '=' || $from[1] == '==')
            {
                $to = $from;

                $to[1]   = '=';
                $from[1] = '=';
            }

            if($from[1] == '>' || $from[1] == '>=')
            {
                $to = [ 999999999999, '<' ];
            }

            if($from[1] == '<' || $from[1] == '<=')
            {
                $to = $from;
                $from = [ 0, '>' ];
            }
        }

        return [
            'from' => $from,
            'to'   => $to
        ];
    }
}
