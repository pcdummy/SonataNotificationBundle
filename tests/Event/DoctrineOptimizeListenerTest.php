<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\NotificationBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use Sonata\NotificationBundle\Event\DoctrineOptimizeListener;
use Sonata\NotificationBundle\Event\IterateEvent;

class DoctrineOptimizeListenerTest extends TestCase
{
    public function testWithClosedManager()
    {
        $this->expectException(\RuntimeException::class);

        $manager = $this->createMock('Doctrine\ORM\EntityManager');
        $manager->expects($this->once())->method('isOpen')->will($this->returnValue(false));

        $registry = $this->createMock('Symfony\Bridge\Doctrine\RegistryInterface');
        $registry->expects($this->once())->method('getManagers')->will($this->returnValue([
            'default' => $manager,
        ]));

        $optimizer = new DoctrineOptimizeListener($registry);
        $optimizer->iterate(new IterateEvent(
            $this->createMock('Sonata\NotificationBundle\Iterator\MessageIteratorInterface'),
            $this->createMock('Sonata\NotificationBundle\Backend\BackendInterface')
        ));
    }

    public function testOptimize()
    {
        $unitofwork = $this->createMock('Doctrine\ORM\UnitOfWork');
        $unitofwork->expects($this->once())->method('clear');

        $manager = $this->createMock('Doctrine\ORM\EntityManager');
        $manager->expects($this->once())->method('isOpen')->will($this->returnValue(true));
        $manager->expects($this->once())->method('getUnitOfWork')->will($this->returnValue($unitofwork));

        $registry = $this->createMock('Symfony\Bridge\Doctrine\RegistryInterface');
        $registry->expects($this->once())->method('getManagers')->will($this->returnValue([
            'default' => $manager,
        ]));

        $optimizer = new DoctrineOptimizeListener($registry);
        $optimizer->iterate(new IterateEvent(
            $this->createMock('Sonata\NotificationBundle\Iterator\MessageIteratorInterface'),
            $this->createMock('Sonata\NotificationBundle\Backend\BackendInterface')
        ));
    }
}
