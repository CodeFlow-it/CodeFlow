```mermaid
graph LR
    A[Vue JS Frontend] -- HTTP Requests --> B[Symfony Backend]
    B -- Data Storage --> C[(MySQL Database)]
    B -- Queue Analysis Jobs --> D[RabbitMQ]
    D -- Initiates Analysis --> E[nodejs containing Exakat]
    D -- Initiates Metrics Collection --> F[nodejs application containing PHP Metrics]
    F -- Conversion to JSON --> G[phpmetrics2json]
    E -- Analysis Results --> D
    G -- Metrics in JSON --> D
    D -- Results to Backend --> B

  ```