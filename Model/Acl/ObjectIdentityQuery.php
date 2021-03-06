<?php

/**
 * This file is part of the PropelBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Propel\Bundle\PropelBundle\Model\Acl;

use Propel\Bundle\PropelBundle\Model\Acl\Base\ObjectIdentityQuery as BaseObjectIdentityQuery;

use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\Connection\ConnectionInterface;

use Symfony\Component\Security\Acl\Model\ObjectIdentityInterface;

class ObjectIdentityQuery extends BaseObjectIdentityQuery
{
    /**
     * Filter by an ObjectIdentity object belonging to the given ACL related ObjectIdentity.
     *
     * @param \Symfony\Component\Security\Acl\Model\ObjectIdentityInterface $objectIdentity
     * @param ConnectionInterface                                           $con
     *
     * @return \Propel\Bundle\PropelBundle\Model\Acl\ObjectIdentityQuery $this
     */
    public function filterByAclObjectIdentity(ObjectIdentityInterface $objectIdentity, ConnectionInterface $con = null)
    {
        /*
         * Not using a JOIN here, because the filter may be applied on 'findOneOrCreate',
         * which is currently (Propel 1.6.4-dev) not working.
         */
        $aclClass = AclClass::fromAclObjectIdentity($objectIdentity, $con);
        $this
            ->filterByClassId($aclClass->getId())
            ->filterByIdentifier($objectIdentity->getIdentifier())
        ;

        return $this;
    }

    /**
     * Return an ObjectIdentity object belonging to the given ACL related ObjectIdentity.
     *
     * @param \Symfony\Component\Security\Acl\Model\ObjectIdentityInterface $objectIdentity
     * @param ConnectionInterface                                           $con
     *
     * @return \Propel\Bundle\PropelBundle\Model\Acl\ObjectIdentity
     */
    public function findOneByAclObjectIdentity(ObjectIdentityInterface $objectIdentity, ConnectionInterface $con = null)
    {
        return $this
            ->filterByAclObjectIdentity($objectIdentity, $con)
            ->findOne($con)
        ;
    }

    /**
     * Return all children of the given object identity.
     *
     * @param \Propel\Bundle\PropelBundle\Model\Acl\ObjectIdentity $objectIdentity
     * @param ConnectionInterface                                  $con
     *
     * @return \PropelObjectCollection
     */
    public function findChildren(ObjectIdentity $objectIdentity, ConnectionInterface $con = null)
    {
        return $this
            ->filterByObjectIdentityRelatedByParentObjectIdentityId($objectIdentity)
            ->find($con)
        ;
    }

    /**
     * Return all children and grand-children of the given object identity.
     *
     * @param \Propel\Bundle\PropelBundle\Model\Acl\ObjectIdentity $objectIdentity
     * @param ConnectionInterface                                  $con
     *
     * @return \PropelObjectCollection
     */
    public function findGrandChildren(ObjectIdentity $objectIdentity, ConnectionInterface $con = null)
    {
        return $this
            ->useObjectIdentityAncestorRelatedByObjectIdentityIdQuery()
                ->filterByObjectIdentityRelatedByAncestorId($objectIdentity)
                ->filterByObjectIdentityRelatedByObjectIdentityId($objectIdentity, Criteria::NOT_EQUAL)
            ->endUse()
            ->find($con)
        ;
    }

    /**
     * Return all ancestors of the given object identity.
     *
     * @param ObjectIdentity      $objectIdentity
     * @param ConnectionInterface $con
     *
     * @return \PropelObjectCollection
     */
    public function findAncestors(ObjectIdentity $objectIdentity, ConnectionInterface $con = null)
    {
        return $this
            ->useObjectIdentityAncestorRelatedByAncestorIdQuery()
                ->filterByObjectIdentityRelatedByObjectIdentityId($objectIdentity)
                ->filterByObjectIdentityRelatedByAncestorId($objectIdentity, Criteria::NOT_EQUAL)
            ->endUse()
            ->find($con)
        ;
    }
}
