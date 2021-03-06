<?php
/**
 * RouterList model test class
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Framework\App\Router;

class ActionListTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Magento\Framework\Config\CacheInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $cacheMock;

    /**
     * @var \Magento\Framework\App\Router\ActionList\Reader | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $actionReaderMock;

    /**
     * @var \Magento\Framework\App\Router\ActionList
     */
    protected $actionList;

    public function setUp()
    {
        $this->objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->cacheMock = $this->getMockBuilder('Magento\Framework\Config\CacheInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->actionReaderMock = $this->getMockBuilder('Magento\Framework\App\Router\ActionList\Reader')
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testConstructorCachedData()
    {
        $this->cacheMock->expects($this->once())
            ->method('load')
            ->will($this->returnValue(serialize('data')));
        $this->cacheMock->expects($this->never())
            ->method('save');
        $this->actionReaderMock->expects($this->never())
            ->method('read');
        $this->actionList = $this->objectManager->getObject(
            'Magento\Framework\App\Router\ActionList',
            [
                'cache' => $this->cacheMock,
                'actionReader' => $this->actionReaderMock,
            ]
        );
    }

    public function testConstructorNoCachedData()
    {
        $this->cacheMock->expects($this->once())
            ->method('load')
            ->will($this->returnValue(false));
        $this->cacheMock->expects($this->once())
            ->method('save');
        $this->actionReaderMock->expects($this->once())
            ->method('read')
            ->will($this->returnValue('data'));
        $this->actionList = $this->objectManager->getObject(
            'Magento\Framework\App\Router\ActionList',
            [
                'cache' => $this->cacheMock,
                'actionReader' => $this->actionReaderMock,
            ]
        );
    }

    /**
     * @param string $module
     * @param string $area
     * @param string $namespace
     * @param string $action
     * @param array $data
     * @param string|null $expected
     * @dataProvider getDataProvider
     */
    public function testGet($module, $area, $namespace, $action, $data, $expected)
    {

        $this->cacheMock->expects($this->once())
            ->method('load')
            ->will($this->returnValue(false));
        $this->cacheMock->expects($this->once())
            ->method('save');
        $this->actionReaderMock->expects($this->once())
            ->method('read')
            ->will($this->returnValue($data));
        $this->actionList = $this->objectManager->getObject(
            'Magento\Framework\App\Router\ActionList',
            [
                'cache' => $this->cacheMock,
                'actionReader' => $this->actionReaderMock,
            ]
        );
        $this->assertEquals($expected, $this->actionList->get($module, $area, $namespace, $action));
    }

    public function getDataProvider()
    {
        $mockClassName = 'Mock_Action_Class';
        $actionClass = $this->getMockClass(
            'Magento\Framework\App\ActionInterface',
            ['dispatch', 'getResponse'],
            [],
            $mockClassName
        );

        return [
            [
                'Magento_Module',
                'Area',
                'Namespace',
                'Index',
                ['magento\module\controller\area\namespace\index' => $mockClassName],
                $actionClass
            ],
            [
                'Magento_Module',
                '',
                'Namespace',
                'Index',
                ['magento\module\controller\namespace\index' => $mockClassName],
                $actionClass
            ],
            [
                'Magento_Module',
                'Area',
                'Namespace',
                'Catch',
                ['magento\module\controller\area\namespace\catchaction' => $mockClassName],
                $actionClass
            ],
            [
                'Magento_Module',
                'Area',
                'Namespace',
                'Index',
                ['magento\module\controller\area\namespace\index' => 'Not_Exist_Class'],
                null
            ],
            [
                'Magento_Module',
                'Area',
                'Namespace',
                'Index',
                [],
                null
            ],
        ];
    }
}
