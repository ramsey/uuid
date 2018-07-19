<?php

use Sami\Sami;
use Sami\RemoteRepository\GitHubRemoteRepository;
use Sami\Version\GitVersionCollection;
use Symfony\Component\Finder\Finder;

$projectRoot = realpath(__DIR__ . '/..');
$source = $projectRoot . '/src';

$iterator = Finder::create()
    ->files()
    ->name('*.php')
    ->in($source);

$versions = GitVersionCollection::create($projectRoot)
    ->addFromTags('3.*');

return new Sami($iterator, array(
    'theme' => 'default',
    'versions' => $versions,
    'title' => 'ramsey/uuid',
    'build_dir' => $projectRoot . '/build/apidocs/%version%',
    'cache_dir' => $projectRoot . '/build/cache/apidocs/%version%',
    'remote_repository' => new GitHubRemoteRepository('ramsey/uuid', $projectRoot),
    'default_opened_level' => 2,
));