# waardepapieren chart

The Helm chart installs waardepapieren and by default the following dependencies using subcharts:

- [PostgreSQL](https://github.com/bitnami/charts/tree/master/bitnami/postgresql)
- [Redis](https://github.com/bitnami/charts/tree/master/bitnami/redis)

## Installation

First configure the Helm repository:

```bash
helm repo add waardepapieren https://raw.githubusercontent.com/ConductionNL/waardepapieren/master/api/helm/
helm repo update
```

Install the Helm chart with:

```bash
helm install my-waardepapieren ConductionNL/waardepapieren --version 0.1.0
```

:warning: The default settings are unsafe for production usage. Configure proper secrets, enable persistence and consider High Availability (HA) for the database and the application.

## Configuration

| Parameter | Description | Default |
| --------- | ----------- | ------- |
| `settings.domain` | The domain (if any) that you want to deploy to | `conduction.nl` |
| `settings.subdomain` | the subdomain of the installation excluding www. | `waardepapieren` |
| `settings.subpath` | Any sub path to follow the domain, like /api/v1 | `waardepapieren` |
| `settings.subpathRouting` | Whether to actually use te sub path | false |
| `settings.env` | Either prod or dev, determines settings like error tracing | `dev` |
| `settings.web` | Whether tot start an ingress in way | false |
| `settings.debug` | Run te application in debug mode | 1 |
| `settings.cache` | Activate resource caching | false |
| `settings.corsAllowOrigin` | Set the cors header | `['*']` |
| `settings.trustedHosts` | A regex function for whitelisting ips | '^.+$' |
| `settings.pullPolicy` | When to pull new images | `Always` |

Check [values.yaml](./values.yaml) for all the possible configuration options.

# Deploying to a Kubernetes Cluster

API Platform comes with a native integration with [Kubernetes](https://kubernetes.io/) and the [Helm](https://helm.sh/)
package manager.

[Learn how to deploy in the dedicated documentation entry](https://api-platform.com/docs/deployment/kubernetes/).
