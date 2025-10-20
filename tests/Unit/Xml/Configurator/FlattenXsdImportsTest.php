<?php
declare(strict_types=1);

namespace Soap\Wsdl\Test\Unit\Xml\Configurator;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Soap\Wsdl\Loader\Context\FlatteningContext;
use Soap\Wsdl\Loader\StreamWrapperLoader;
use Soap\Wsdl\Xml\Configurator\FlattenXsdImports;
use VeeWee\Xml\Dom\Document;
use function VeeWee\Xml\Dom\Configurator\canonicalize;
use function VeeWee\Xml\Dom\Configurator\comparable;

final class FlattenXsdImportsTest extends TestCase
{
    #[DataProvider('provideTestCases')]
    public function test_it_can_flatten_xsd_imports(string $wsdl, Document $expected, callable $xmlConfigurator): void
    {
        $wsdlDoc = Document::fromXmlFile($wsdl);
        $configurator = new FlattenXsdImports(
            $wsdl,
            FlatteningContext::forWsdl($wsdl, $wsdlDoc, new StreamWrapperLoader())
        );
        $flattened = Document::fromUnsafeDocument($wsdlDoc->toUnsafeDocument(), $configurator, $xmlConfigurator);

        static::assertSame($expected->reconfigure($xmlConfigurator)->toXmlString(), $flattened->toXmlString());
    }

    public static function provideTestCases()
    {
        yield 'single-xsd' => [
            'wsdl' => FIXTURE_DIR.'/flattening/single-xsd.wsdl',
            'expected' => Document::fromXmlFile(FIXTURE_DIR.'/flattening/result/single-xsd-result.wsdl'),
            "xmlConfigurator" => comparable(),
        ];
        yield 'once-xsd' => [
            'wsdl' => FIXTURE_DIR.'/flattening/once-xsd.wsdl',
            'expected' => Document::fromXmlFile(FIXTURE_DIR.'/flattening/result/once-xsd-result.wsdl'),
            "xmlConfigurator" => comparable(),
        ];
        yield 'multi-xsd' => [
            'wsdl' => FIXTURE_DIR.'/flattening/multi-xsd.wsdl',
            'expected' => Document::fromXmlFile(FIXTURE_DIR.'/flattening/result/multi-xsd-result.wsdl'),
            "xmlConfigurator" => comparable(),
        ];
        yield 'circular-xsd' => [
            'wsdl' => FIXTURE_DIR.'/flattening/circular-xsd.wsdl',
            'expected' => Document::fromXmlFile(FIXTURE_DIR.'/flattening/result/circular-xsd-result.wsdl'),
            "xmlConfigurator" => comparable(),
        ];
        yield 'redefine-xsd' => [
            'wsdl' => FIXTURE_DIR.'/flattening/redefine-xsd.wsdl',
            'expected' => Document::fromXmlFile(FIXTURE_DIR.'/flattening/result/redefine-xsd-result.wsdl'),
            "xmlConfigurator" => comparable(),
        ];
        yield 'tnsless-xsd' => [
            'wsdl' => FIXTURE_DIR.'/flattening/tnsless-xsd.wsdl',
            'expected' => Document::fromXmlFile(FIXTURE_DIR.'/flattening/result/tnsless-xsd-result.wsdl'),
            "xmlConfigurator" => comparable(),
        ];
        yield 'grouped-xsd' => [
            'wsdl' => FIXTURE_DIR.'/flattening/grouped-xsd.wsdl',
            'expected' => Document::fromXmlFile(FIXTURE_DIR.'/flattening/result/grouped-xsd-result.wsdl'),
            "xmlConfigurator" => comparable(),
        ];
        yield 'root-xmlns-import-issue' => [
            'wsdl' => FIXTURE_DIR.'/flattening/root-xmlns-import-issue.wsdl',
            'expected' => Document::fromXmlFile(FIXTURE_DIR.'/flattening/result/root-xmlns-import-issue-result.wsdl'),
            "xmlConfigurator" => canonicalize(),
        ];
        yield 'rearranged-imports' => [
            'wsdl' => FIXTURE_DIR.'/flattening/rearranged-imports.wsdl',
            'expected' => Document::fromXmlFile(FIXTURE_DIR.'/flattening/result/rearranged-imports.wsdl'),
            "xmlConfigurator" => comparable(),
        ];
        yield 'import-xmlns-issue' => [
            'wsdl' => FIXTURE_DIR.'/flattening/conflicting-imports.wsdl',
            'expected' => Document::fromXmlFile(FIXTURE_DIR.'/flattening/result/conflicting-imports.wsdl'),
            "xmlConfigurator" => canonicalize(),
        ];
    }
}
