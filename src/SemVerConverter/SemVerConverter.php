<?php

namespace Requtize\SemVerConverter;

use Composer\Semver\VersionParser;
use Composer\Semver\Constraint\Constraint;
use Composer\Semver\Constraint\MultiConstraint;

class SemVerConverter
{
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

        // Multiply every section by 100
        $sections = array_map(function ($val) {
            return str_pad($val, 3, 0);
        }, $sections);

        $result = '';

        foreach($sections as $section)
        {
            $result .= $section == 0 ? '000' : $section;
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
