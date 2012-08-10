<?php
/**
 * This file is part of the Rhumsaa\Uuid library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Copyright (c) 2012 Ben Ramsey <http://benramsey.com>
 * @license http://opensource.org/licenses/MIT MIT
 */

namespace Rhumsaa\Uuid\Symfony\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * @Annotation
 */
class UuidValidator extends ConstraintValidator
{
    /**
     * @var string
     */
    const PATTERN = '/^[a-z0-9]{8}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{12}$/';

    public function isValid($value, Constraint $constraint)
    {
        $isValid = true;

        if (!preg_match(self::PATTERN, $value, $matches)) {
            // TODO: Should the UUID be escaped first...?
            $this->setMessage($constraint->message, array('%uuid%' => $value));

            $isValid = false;
        }

        return $isValid;
    }
}
