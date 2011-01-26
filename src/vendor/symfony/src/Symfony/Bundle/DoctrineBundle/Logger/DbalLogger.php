<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Bundle\DoctrineBundle\Logger;

use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Doctrine\DBAL\Logging\DebugStack;

/**
 * DbalLogger.
 *
 * @author Fabien Potencier <fabien.potencier@symfony-project.com>
 */
class DbalLogger extends DebugStack
{
    protected $logger;

    /**
     * Constructor.
     *
     * @param LoggerInterface $logger A LoggerInterface instance
     */
    public function __construct(LoggerInterface $logger = null)
    {
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function startQuery($sql, array $params = null, array $types = null)
    {
        parent::startQuery($sql, $params, $types);

        if (null !== $this->logger) {
            $this->log($sql.' ('.str_replace("\n", '', var_export($params, true)).')');
        }
    }

    /**
     * Logs a message.
     *
     * @param string $message A message to log
     */
    public function log($message)
    {
        $this->logger->info($message);
    }
}