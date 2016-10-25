<?php

namespace Clue\Commander\Tokens;

use InvalidArgumentException;

class EllipseToken implements TokenInterface
{
    public function __construct(TokenInterface $token)
    {
        if ($token instanceof self) {
            throw new InvalidArgumentException('Nested ellipse block is superfluous');
        }

        $this->token = $token;
    }

    public function matches(array &$input, array &$output)
    {
        $soutput = $output;
        if ($this->token->matches($input, $output)) {
            // array of all new output variables
            $all = array();

            do {
                // check new output against original output
                foreach ($output as $name => $value) {
                    if (!isset($soutput[$name]) || $soutput[$name] !== $value) {
                        $all[$name][] = $value;
                    }
                }

                // reset output to original state and try next match
                $output = $soutput;
            } while($this->token->matches($input, $output));

            // output is new output variables plus original variables
            $output = $all + $soutput;

            return true;
        }

        return false;
    }

    public function __toString()
    {
        return $this->token . '...';
    }
}
