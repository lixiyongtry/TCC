<?php declare(strict_types=1);

namespace Six\Rpc\Client\Annotation\Mapping;
use Doctrine\Common\Annotations\Annotation\Attribute;
use Doctrine\Common\Annotations\Annotation\Attributes;
use Doctrine\Common\Annotations\Annotation\Required;
use Doctrine\Common\Annotations\Annotation\Target;
use Swoft\Rpc\Protocol;

/**
 *
 * @Annotation
 * @Target("CLASS")
 * @Attributes({
 *     @Attribute("event", type="string"),
 * })
 */
class Fallback
{
    /**
     * @var string
     *
     * @Required()
     */
    private $name;
    /**
     * @var string
     */
    private $version = Protocol::DEFAULT_VERSION;

    /**
     * Reference constructor.
     *
     * @param array $values
     */
    public function __construct(array $values)
    {
        if (isset($values['value'])) {
            $this->pool = $values['value'];
        } elseif (isset($values['name'])) {
            $this->name = $values['name'];
        }
        if (isset($values['version'])) {
            $this->version = $values['version'];
        }
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}