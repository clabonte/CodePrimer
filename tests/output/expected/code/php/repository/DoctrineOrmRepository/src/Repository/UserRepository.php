<?php
/*
 * This file has been generated by CodePrimer.io
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CodePrimer\Tests\Repository;

use CodePrimer\Tests\Entity\User;
use CodePrimer\Tests\Entity\UserStats;
use CodePrimer\Tests\Entity\Subscription;
use CodePrimer\Tests\Entity\Metadata;
use CodePrimer\Tests\Entity\Post;
use CodePrimer\Tests\Entity\Topic;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Psr\Log\LoggerInterface;

/**
 * Class UserRepository
 * Manipulates User entities with the persistence layer.
 * @package CodePrimer\Tests\Repository
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    /** @var LoggerInterface */
    private $logger;

    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @required
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Retrieves a single User based on the uniqueEmail constraint
     * @param string $email
     * @return User|null
     */
    public function getByUniqueEmail(string $email): ?User
    {
        try {
            return $this->createQueryBuilder('u')
                ->andWhere('u.email = :email')
                ->setParameter('email', $email)
                ->getQuery()
                ->getSingleResult();
        } catch (NoResultException $e) {
            $this->logger->notice('No result found: ' .$e->getMessage(), [
                'email' => $email,
                'exception' => $e
            ]);
        } catch (NonUniqueResultException $e) {
            $this->logger->error('Multiple results found: ' .$e->getMessage(), [
                'email' => $email,
                'exception' => $e
            ]);
        }

        return null;
    }

    /**
     * Retrieves a single User based on the uniqueNickname constraint
     * @param string $nickname
     * @return User|null
     */
    public function getByUniqueNickname(string $nickname): ?User
    {
        try {
            return $this->createQueryBuilder('u')
                ->andWhere('u.nickname = :nickname')
                ->setParameter('nickname', $nickname)
                ->getQuery()
                ->getSingleResult();
        } catch (NoResultException $e) {
            $this->logger->notice('No result found: ' .$e->getMessage(), [
                'nickname' => $nickname,
                'exception' => $e
            ]);
        } catch (NonUniqueResultException $e) {
            $this->logger->error('Multiple results found: ' .$e->getMessage(), [
                'nickname' => $nickname,
                'exception' => $e
            ]);
        }

        return null;
    }

    /**
     * Retrieves a User linked to a given UserStats
     * @param UserStats $stat
     * @return User|null
     */
    public function getByUserStats(UserStats $stat): ?User
    {
        try {
            return $this->createQueryBuilder('u')
                ->andWhere('u.stat = :stat')
                ->setParameter('stat', $stat)
                ->getQuery()
                ->getSingleResult();
        } catch (NoResultException $e) {
            $this->logger->notice('No result found: ' .$e->getMessage(), [
                'stat' => $stat,
                'exception' => $e
            ]);
        } catch (NonUniqueResultException $e) {
            $this->logger->error('Multiple results found: '.$e->getMessage(), [
                'stat' => $stat,
                'exception' => $e
            ]);
        }

        return null;
    }

    /**
     * Retrieves a User linked to a given Subscription
     * @param Subscription $subscription
     * @return User|null
     */
    public function getBySubscription(Subscription $subscription): ?User
    {
        try {
            return $this->createQueryBuilder('u')
                ->andWhere('u.subscription = :subscription')
                ->setParameter('subscription', $subscription)
                ->getQuery()
                ->getSingleResult();
        } catch (NoResultException $e) {
            $this->logger->notice('No result found: ' .$e->getMessage(), [
                'subscription' => $subscription,
                'exception' => $e
            ]);
        } catch (NonUniqueResultException $e) {
            $this->logger->error('Multiple results found: '.$e->getMessage(), [
                'subscription' => $subscription,
                'exception' => $e
            ]);
        }

        return null;
    }

    /**
     * Retrieves a User linked to a given Metadata
     * @param Metadata $metadatum
     * @return User|null
     */
    public function getByMetadata(Metadata $metadatum): ?User
    {
        try {
            return $this->createQueryBuilder('u')
                ->andWhere('u.metadata = :metadata')
                ->setParameter('metadata', $metadatum)
                ->getQuery()
                ->getSingleResult();
        } catch (NoResultException $e) {
            $this->logger->notice('No result found: ' .$e->getMessage(), [
                'metadata' => $metadatum,
                'exception' => $e
            ]);
        } catch (NonUniqueResultException $e) {
            $this->logger->error('Multiple results found: '.$e->getMessage(), [
                'metadata' => $metadatum,
                'exception' => $e
            ]);
        }

        return null;
    }

    /**
     * Retrieves a User linked to a given Post
     * @param Post $post
     * @return User|null
     */
    public function getByPost(Post $post): ?User
    {
        try {
            return $this->createQueryBuilder('u')
                ->andWhere('u.post = :post')
                ->setParameter('post', $post)
                ->getQuery()
                ->getSingleResult();
        } catch (NoResultException $e) {
            $this->logger->notice('No result found: ' .$e->getMessage(), [
                'post' => $post,
                'exception' => $e
            ]);
        } catch (NonUniqueResultException $e) {
            $this->logger->error('Multiple results found: '.$e->getMessage(), [
                'post' => $post,
                'exception' => $e
            ]);
        }

        return null;
    }

    /**
     * Retrieves a list of User linked to a given Topic
     * @param Topic $topic
     * @param bool $mostRecentFirst
     * @return User[]
     */
    public function findAllByTopic(Topic $topic, bool $mostRecentFirst = true): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.topic = :topic')
            ->setParameter('topic', $topic)
            ->orderBy('u.created', $mostRecentFirst ? 'DESC' : 'ASC')
            ->getQuery()
            ->getResult();
    }

}
