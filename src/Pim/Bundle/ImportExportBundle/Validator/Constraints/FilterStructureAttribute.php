<?php

namespace Pim\Bundle\ImportExportBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @author    Philippe Mossière <philippe.mossiere@akeneo.com>
 * @copyright 2016 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
class FilterStructureAttribute extends Constraint
{
    /** @var string */
    public $message = 'The attribute %attributeCode% is not valid.';

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return 'filter_structure_attribute_validator';
    }
}