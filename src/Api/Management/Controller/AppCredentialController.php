<?php

namespace Apigee\Edge\Api\Management\Controller;

use Apigee\Edge\Api\Management\Entity\AppCredentialInterface;
use Apigee\Edge\Entity\EntityController;
use Apigee\Edge\Entity\EntityCrudOperationsTrait;
use Apigee\Edge\Entity\EntityInterface;
use Apigee\Edge\Entity\StatusAwareEntityControllerTrait;
use Apigee\Edge\Structure\AttributesProperty;
use Apigee\Edge\Structure\KeyValueMapNormalizer;
use Psr\Http\Message\UriInterface;

/**
 * Class AppCredentialController.
 *
 * @package Apigee\Edge\Api\Management\Controller
 * @author Dezső Biczó <mxr576@gmail.com>
 */
class AppCredentialController extends EntityController implements AppCredentialControllerInterface
{
    use StatusAwareEntityControllerTrait;

    use EntityCrudOperationsTrait {
        // These methods are not supported on this endpoint in the same way as on the others so do not allow to
        // use them here.
        EntityCrudOperationsTrait::create as private privateCreate;
        EntityCrudOperationsTrait::update as private privateUpdate;
    }

    /** @var string Developer email or id. */
    protected $developerId;

    /** @var string App name. */
    protected $appName;

    /**
     * AppCredentialController constructor.
     *
     * @param string $organization
     * @param string $developerId
     * @param string $appName
     * @param null $client
     * @param null $entityFactory
     */
    public function __construct(
        string $organization,
        string $developerId,
        string $appName,
        $client = null,
        $entityFactory = null
    ) {
        parent::__construct($organization, $client, $entityFactory);
        $this->developerId = $developerId;
        $this->appName = $appName;
    }

    /**
     * @inheritdoc
     */
    protected function getBaseEndpointUri(): UriInterface
    {
        return $this->client->getUriFactory()
            ->createUri(sprintf(
                '/organizations/%s/developers/%s/apps/%s',
                $this->organization,
                $this->developerId,
                $this->appName
            ));
    }

    /**
     * @inheritdoc
     */
    protected function getEntityEndpointUri(string $entityId): UriInterface
    {
        return $this->getBaseEndpointUri()->withPath(sprintf('%s/keys/%s', $this->getBaseEndpointUri(), $entityId));
    }

    /**
     * @inheritdoc
     */
    public function create(string $consumerKey, string $consumerSecret): AppCredentialInterface
    {
        $response = $this->client->post(
            // Just to spare some extra lines of code.
            $this->getEntityEndpointUri('create'),
            json_encode((object)['consumerKey' => $consumerKey, 'consumerSecret' => $consumerSecret])
        );
        return $this->entitySerializer->deserialize(
            $response->getBody(),
            $this->entityFactory->getEntityTypeByController(AppCredentialController::class),
            'json'
        );
    }

    /**
     * @inheritdoc
     */
    public function generate(
        array $apiProducts,
        AttributesProperty $attributes,
        string $keyExpiresIn = '-1'
    ): AppCredentialInterface {
        $normalizer = new KeyValueMapNormalizer();
        $response = $this->client->post(
            $this->getBaseEndpointUri(),
            json_encode((object)[
                'apiProducts' => $apiProducts,
                'attributes' => $normalizer->normalize($attributes),
                'keyExpiresIn' => $keyExpiresIn
            ])
        );
        // It returns a complete developer app entity, but we only returns the newly created credential for the
        // sake of consistency.
        $responseArray = $this->parseResponseToArray($response);
        $credentialArray = reset($responseArray['credentials']);
        return $this->entitySerializer->denormalize(
            $credentialArray,
            $this->entityFactory->getEntityTypeByController(AppCredentialController::class)
        );
    }

    /**
     * @inheritdoc
     */
    public function addProducts(string $consumerKey, array $apiProducts): AppCredentialInterface
    {
        $response = $this->client->post(
            $this->getEntityEndpointUri($consumerKey),
            json_encode((object)['apiProducts' => $apiProducts])
        );
        return $this->entitySerializer->deserialize(
            $response->getBody(),
            $this->entityFactory->getEntityTypeByController(AppCredentialController::class),
            'json'
        );
    }

    /**
     * @inheritdoc
     */
    public function overrideAttributes(string $consumerKey, AttributesProperty $attributes): AppCredentialInterface
    {
        $normalizer = new KeyValueMapNormalizer();
        $response = $this->client->post(
            $this->getEntityEndpointUri($consumerKey),
            json_encode((object)['attributes' => $normalizer->normalize($attributes)])
        );
        return $this->entitySerializer->deserialize(
            $response->getBody(),
            $this->entityFactory->getEntityTypeByController(AppCredentialController::class),
            'json'
        );
    }

    /**
     * @inheritdoc
     */
    public function setApiProductStatus(string $consumerKey, string $apiProduct, string $status): void
    {
        $uri = $this->getBaseEndpointUri()
            ->withPath(sprintf('%s/keys/%s/apiproducts/%s', $this->getBaseEndpointUri(), $consumerKey, $apiProduct))
            ->withQuery(http_build_query(['action' => $status]));
        $this->client->post($uri, null, ['Content-Type' => 'application/octet-stream']);
    }

    /**
     * @inheritdoc
     */
    public function deleteApiProduct(string $consumerKey, string $apiProduct): EntityInterface
    {
        $uri = $this->getBaseEndpointUri()
            ->withPath(sprintf('%s/keys/%s/apiproducts/%s', $this->getBaseEndpointUri(), $consumerKey, $apiProduct));
        $response = $this->client->delete($uri);
        return $this->entitySerializer->deserialize(
            $response->getBody(),
            $this->entityFactory->getEntityTypeByController($this),
            'json'
        );
    }

    /**
     * @inheritdoc
     */
    public function overrideScopes(string $consumerKey, array $scopes): AppCredentialInterface
    {
        $response = $this->client->post(
            $this->getEntityEndpointUri($consumerKey),
            json_encode((object)['scopes' => $scopes])
        );
        return $this->entitySerializer->deserialize(
            $response->getBody(),
            $this->entityFactory->getEntityTypeByController(AppCredentialController::class),
            'json'
        );
    }
}
