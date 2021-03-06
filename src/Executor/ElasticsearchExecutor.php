<?php

namespace RulerZ\Executor;

use Elasticsearch\Client;
use Hoa\Ruler\Model;

use RulerZ\Context\ExecutionContext;
use RulerZ\Visitor\ElasticsearchVisitor;

/**
 * Execute a rule on an Elasticsearch client.
 */
class ElasticsearchExecutor implements ExtendableExecutor
{
    use Polyfill\ExtendableExecutor;
    use Polyfill\FilterBasedSatisfaction;

    /**
     * Constructs the Elasticsearch executor.
     *
     * @param array $operators A list of custom operators to register.
     */
    public function __construct(array $operators = [])
    {
        $this->registerOperators($operators);
    }

    /**
     * {@inheritDoc}
     */
    public function filter($target, Model $rule, array $parameters, ExecutionContext $context)
    {
        if (empty($context['index']) || empty($context['type'])) {
            throw new \BadMethodCallException('Parameters "index" and "type" are mandatory for the ElasticsearchExecutor"');
        }

        $searchQuery = $this->buildSearchQuery($rule, $parameters);

        return $target->search([
            'index' => $context['index'],
            'type'  => $context['type'],
            'body'  => ['query' => $searchQuery],
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function supports($target, $mode)
    {
        return $target instanceof Client;
    }

    /**
     * Builds the search query for the given rule.
     *
     * @param Model $rule       The rule to apply.
     * @param array $parameters The search parameters.
     *
     * @return array The search.
     */
    private function buildSearchQuery(Model $rule, array $parameters)
    {
        $searchBuilder = new ElasticsearchVisitor();
        $searchBuilder->setOperators($this->getOperators());
        $searchBuilder->setParameters($parameters);

        return $searchBuilder->visit($rule);
    }
}
