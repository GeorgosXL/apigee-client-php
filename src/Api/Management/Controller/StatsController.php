<?php

namespace Apigee\Edge\Api\Management\Controller;

use Apigee\Edge\Api\Management\Query\StatsQueryInterface;
use Apigee\Edge\Api\Management\Query\StatsQueryNormalizer;
use Apigee\Edge\Controller\AbstractController;
use Apigee\Edge\Controller\OrganizationAwareControllerTrait;
use Apigee\Edge\HttpClient\ClientInterface;
use League\Period\Period;
use Psr\Http\Message\UriInterface;
use Symfony\Component\Serializer\Encoder\JsonDecode;

/**
 * Class StatsController.
 */
class StatsController extends AbstractController implements StatsControllerInterface
{
    use OrganizationAwareControllerTrait;

    /** @var string */
    private $environment;

    /** @var \Apigee\Edge\Api\Management\Query\StatsQueryNormalizer */
    private $normalizer;

    /**
     * StatsController constructor.
     *
     * @param string $environment
     *   The environment name.
     * @param string $organization
     *   Name of the organization that the entities belongs to.
     * @param ClientInterface|null $client
     *   Apigee Edge API client.
     */
    public function __construct(string $environment, string $organization, ClientInterface $client = null)
    {
        parent::__construct($client);
        $this->environment = $environment;
        $this->organization = $organization;
        $this->normalizer = new StatsQueryNormalizer();
        // Return responses as an associative array instead of in Apigee Edge's mixed object-array structure to
        // make developer's life easier.
        $this->jsonDecoder = new JsonDecode(true);
    }

    /**
     * @inheritdoc
     *
     * @psalm-suppress InvalidOperand - $this->normalizer->normalize() always returns an array.
     */
    public function getMetrics(StatsQueryInterface $query, ?string $optimized = 'js'): array
    {
        $query_params = [
                '_optimized' => $optimized,
            ] + $this->normalizer->normalize($query);
        $uri = $this->getBaseEndpointUri()->withQuery(http_build_query($query_params));
        $response = $this->responseToArray($this->client->get($uri));

        return $response['Response'];
    }

    /**
     * Gets API message count.
     *
     * The additional optimization on the returned data happens in the SDK. The SDK fills the gaps between returned time
     * units and analytics numbers in the returned response of Apigee Edge.
     * (This method also asks optimized response from Apigee Edge too.)
     *
     * @param StatsQueryInterface $query
     *   Stats query object.
     *
     * @return array
     *   Response as associative array.
     *
     * @psalm-suppress PossiblyNullArgument - $query->getTimeUnit() is not null.
     */
    public function getOptimisedMetrics(StatsQueryInterface $query): array
    {
        $response = $this->getMetrics($query, 'js');
        if (null !== $query->getTimeUnit()) {
            $originalTimeUnits = $response['TimeUnit'];
            $response['TimeUnit'] = $this->fillGapsInTimeUnitsData($query->getTimeRange(), $query->getTimeUnit(), $query->getTsAscending());
            $this->fillGapsInMetricsData(
                $query->getTsAscending(),
                $response['TimeUnit'],
                $originalTimeUnits,
                $response['stats']['data']
            );
        }

        return $response;
    }

    /**
     * @inheritdoc
     *
     * @psalm-suppress InvalidOperand - $this->normalizer->normalize() always returns an array.
     */
    public function getMetricsByDimensions(array $dimensions, StatsQueryInterface $query, ?string $optimized = 'js'): array
    {
        $query_params = [
                '_optimized' => $optimized,
            ] + $this->normalizer->normalize($query);
        $path = $this->getBaseEndpointUri()->getPath() . implode(',', $dimensions);
        $uri = $this->getBaseEndpointUri()->withPath($path)
            ->withQuery(http_build_query($query_params));
        $response = $this->responseToArray($this->client->get($uri));

        return $response['Response'];
    }

    /**
     * Gets optimized metrics organized by dimensions.
     *
     * The additional optimization on the returned data happens in the SDK. The SDK fills the gaps between returned time
     * units and analytics numbers in the returned response of Apigee Edge.
     * (This method also asks optimized response from Apigee Edge too.)
     *
     * @param array $dimensions
     *   Array of dimensions.
     * @param StatsQueryInterface $query
     *   Stats query object.
     *
     * @return array
     *   Response as associative array.
     *
     * @psalm-suppress PossiblyNullArgument - $query->getTimeUnit() is not null.
     */
    public function getOptimizedMetricsByDimensions(array $dimensions, StatsQueryInterface $query): array
    {
        $response = $this->getMetricsByDimensions($dimensions, $query, 'js');
        if (null !== $query->getTimeUnit()) {
            $originalTimeUnits = $response['TimeUnit'];
            $response['TimeUnit'] = $this->fillGapsInTimeUnitsData($query->getTimeRange(), $query->getTimeUnit(), $query->getTsAscending());
            foreach ($response['stats']['data'] as $key => $dimension) {
                $this->fillGapsInMetricsData(
                    $query->getTsAscending(),
                    $response['TimeUnit'],
                    $originalTimeUnits,
                    $response['stats']['data'][$key]['metric']
                );
            }
        }

        return $response;
    }

    /**
     * @inheritdoc
     */
    protected function getBaseEndpointUri(): UriInterface
    {
        // Slash in the end is always required.
        return $this->client->getUriFactory()
            ->createUri(sprintf('/organizations/%s/environments/%s/stats/', $this->organization, $this->environment));
    }

    /**
     * Fills the gaps between returned time units in the response of Apigee Edge.
     *
     * When there were no metric data for a time unit (hour, day, etc.) then those are missing from Apigee Edge response.
     * This utility function fixes this problem.
     *
     * @param Period $period
     *   Original time range from StatsQuery.
     * @param string $timeUnit
     *   Time unit from StatsQuery.
     * @param bool $tsAscending
     *
     * @return array
     *   Array of time units in the given period.
     */
    private function fillGapsInTimeUnitsData(Period $period, string $timeUnit, bool $tsAscending)
    {
        $allTimeUnits = [];
        // Fix time unit for DatePeriod calculation.
        $timeUnit = '1 ' . $timeUnit;
        /** @var \DateTime $dateTime */
        foreach ($period->getDatePeriod($timeUnit) as $dateTime) {
            $allTimeUnits[] = $dateTime->getTimestamp() * 1000;
        }

        return $tsAscending ? $allTimeUnits : array_reverse($allTimeUnits);
    }

    /**
     * Fills the gaps between returned analytics numbers in the response of Apigee Edge.
     *
     * Apigee Edge does not returns zeros for those time units (hours, days, etc.) when there were no metric data.
     *
     * @param bool $tsAscending
     *   TsAscending from StatsQuery.
     * @param array $originalTimeUnits
     *   Returned time units by Apigee Edge.
     * @param array $metricsData
     *   Returned metrics data by Apigee Edge.
     */
    private function fillGapsInMetricsData(bool $tsAscending, array $allTimeUnits, array $originalTimeUnits, array &$metricsData): void
    {
        $zeroArray = array_fill_keys($allTimeUnits, 0);
        foreach ($metricsData as $key => $metric) {
            $metricsData[$key]['values'] = array_combine($originalTimeUnits, $metric['values']);
            $metricsData[$key]['values'] += $zeroArray;
            if ($tsAscending) {
                ksort($metricsData[$key]['values']);
            } else {
                krsort($metricsData[$key]['values']);
            }
            // Keep original numerical indexes.
            $metricsData[$key]['values'] = array_values($metricsData[$key]['values']);
        }
    }
}
