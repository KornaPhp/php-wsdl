<?php

declare(strict_types=1);

namespace Soap\Wsdl\Uri;

use League\Uri\Modifier;
use League\Uri\Uri;
use function Psl\Str\starts_with;

final class IncludePathBuilder
{
    public static function build(string $relativePath, string $fromFile): string
    {
        $baseUri = match(true) {
            starts_with($fromFile, '/') => Uri::fromUnixPath($fromFile),
            default => Uri::new($fromFile),
        };

        $resolvedUri = $baseUri->resolve($relativePath);
        $modifier = Modifier::wrap($resolvedUri)
            ->removeDotSegments()
            ->removeEmptySegments();

        /**
         * @var Uri $relativeUri
         * @psalm-suppress UndefinedClass PHP's URI classes are only available from PHP 8.5
         */
        $relativeUri = $modifier->unwrap();

        if ($relativeUri->getScheme() === 'file') {
            return $relativeUri->getPath();
        }

        return $relativeUri->toString();
    }
}
