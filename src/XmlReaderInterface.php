<?php
namespace Icecave\Siphon;

use SimpleXMLElement;

/**
 * Read XML data based on a resource name.
 */
interface XmlReaderInterface extends ReaderInterface
{
    /**
     * Read XML data for the given resource.
     *
     * @param string               $resource   The path to the resource to read.
     * @param array<string, mixed> $parameters Additional parameters to pass.
     *
     * @return SimpleXMLElement The XML response.
     */
    public function read($resource, array $parameters = []);
}
