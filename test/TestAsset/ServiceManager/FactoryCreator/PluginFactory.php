<?php
/**
 * CampsiteIdInputFactory
 *
 * @category  AcsiCampsite\ServiceFactory\InputFilter
 * @package   AcsiCampsite\ServiceFactory\InputFilter
 * @copyright 2016 ACSI Holding bv (http://www.acsi.eu)
 * @version   SVN: $Id: $
 */

namespace AcsiCampsite\ServiceFactory\InputFilter;

use AcsiCampsite\Repository\CampsiteRepository;
use AcsiCampsite\Validator\CampsiteIdValidator;
use DoctrineModule\Validator\ObjectExists;
use Zend\InputFilter\Input;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class PluginFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $inputFilterPluginManager
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $inputFilterPluginManager)
    {
        $input = (new Input())->setRequired(true);

        $input->getValidatorChain()->attach(new CampsiteIdValidator());
        $input->getValidatorChain()->attach(
            new ObjectExists(
                [
                    'object_repository' => $inputFilterPluginManager->getServiceLocator()->get(CampsiteRepository::class),
                    'fields' => ['campsiteID'],
                    'messages' => [
                        ObjectExists::ERROR_NO_OBJECT_FOUND => 'No campsite exist for the provided campsiteID',
                    ],
                ]
            )
        );

        $input->getFilterChain()->setOptions(
            [
                'filters' => [
                    ['name' => 'StringTrim']
                ]
            ]
        );

        return $input;
    }
}
