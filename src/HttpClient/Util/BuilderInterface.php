<?php

/*
 * Copyright 2018 Google Inc.
 * Use of this source code is governed by a MIT-style license that can be found in the LICENSE file or
 * at https://opensource.org/licenses/MIT.
 */

namespace Apigee\Edge\HttpClient\Util;

use Http\Client\Common\Plugin;
use Http\Client\HttpClient;
use Psr\Cache\CacheItemPoolInterface;

/**
 * Interface BuilderInterface.
 *
 * Describes the public methods of a class that helps in building an Http client.
 */
interface BuilderInterface
{
    /**
     * @return HttpClient
     */
    public function getHttpClient(): HttpClient;

    /**
     * @param array $headers Associate array of HTTP headers.
     */
    public function setHeaders(array $headers): void;

    /**
     * Clear previously set HTTP headers.
     */
    public function clearHeaders(): void;

    /**
     * Add/change header value.
     *
     * @param string $header Header name.
     * @param string $value Header value
     */
    public function setHeaderValue(string $header, string $value): void;

    /**
     * @param string $header Header name.
     */
    public function removeHeader(string $header): void;

    /**
     * Add plugin to the client.
     *
     * @param Plugin $plugin
     *
     * @return mixed
     */
    public function addPlugin(Plugin $plugin): void;

    /**
     * @param string $fqcn Fully qualified class name of the plugin.
     *
     * @return mixed
     */
    public function removePlugin(string $fqcn): void;

    /**
     * Remove all previously added  plugins from the client.
     */
    public function clearPlugins(): void;

    /**
     * Add cache to the client.
     *
     * @param CacheItemPoolInterface $cachePool
     * @param array $config
     */
    public function addCache(CacheItemPoolInterface $cachePool, array $config = []): void;

    /**
     * Remove cache from the client.
     */
    public function removeCache(): void;
}
