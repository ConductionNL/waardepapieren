# Installation
This document dives a little bit deeper into installing your component on a kubernetes cluster, looking for information on setting up your component on a local machine? Take a look at the [tutorial](TUTORIAL.md) instead. 

## Setting up helm
We first need to be sure the ingress nginx repository of helm and kubernetes is added. We do this using the following command:
```CLI
$ helm repo list
```

If in the output there is no repository 'ingress-nginx' we need to add it:

```CLI
$ helm repo add stable https://kubernetes.github.io/ingress-nginx
```

## Setting up ingress
We need at least one nginx controller per kubernetes kluster, doh optionally we could set on up on a per namebase basis

```CLI
$ helm install ingress-nginx/ingress-nginx --name loadbalancer --kubeconfig kubeconfig.yaml
```

After installing a component we can check that out with 

```CLI
$ kubectl describe ingress pc-dev-ingress -n=kube-system --kubeconfig kubeconfig.yaml
```

## Setting up Kubernetes Dashboard
After we installed helm we can easily use both to install kubernetes dashboard

```CLI
$ kubectl create -f https://raw.githubusercontent.com/kubernetes/dashboard/v2.0.0/aio/deploy/recommended.yaml --kubeconfig kubeconfig.yaml
```

This should return the token, copy it to somewhere save (just the token not the other returned information) and start up a dashboard connection

```CLI
$ kubectl proxy --kubeconfig kubeconfig.yaml
```

This should proxy our dashboard to helm making it available trough our favorite browser and a simple link
```CLI
http://localhost:8001/api/v1/namespaces/kube-system/services/https:dashboard-kubernetes-dashboard:https/proxy/#!/login
```

Then, you can login using the Kubeconfig option and uploading your kubeconfig.

## Deploying trough helm
First we always need to update our dependencies
```CLI
$ helm dependency update ./api/helm
```

And then we need to setup the desired namespaces
```CLI
$ kubectl create namespace dev
$ kubectl create namespace stag
$ kubectl create namespace prod
```

If you want to create a new instance
```CLI
$ helm install pc-dev ./api/helm  --kubeconfig kubeconfig.yaml --namespace dev  --set settings.env=dev,settings.debug=1
$ helm install pc-stag ./api/helm --kubeconfig kubeconfig.yaml --namespace stag --set settings.env=stag,settings.debug=0,settings.cache=1
$ helm install pc-prod ./api/helm --kubeconfig kubeconfig.yaml --namespace prod --set settings.env=prod,settings.debug=0,settings.cache=1
```

Or update if you want to update an existing one
```CLI
$ helm upgrade pc-dev ./api/helm  --kubeconfig kubeconfig.yaml --namespace dev  --set settings.env=dev,settings.debug=1
$ helm upgrade pc-stag ./api/helm --kubeconfig kubeconfig.yaml --namespace stag --set settings.env=stag,settings.debug=0,settings.cache=1
$ helm upgrade pc-prod ./api/helm --kubeconfig kubeconfig.yaml --namespace prod --set settings.env=prod,settings.debug=0,settings.cache=1
```

Or just restart the containers of the component
```CLI
$ kubectl rollout restart deployments/pc-php --namespace dev --kubeconfig kubeconfig.yaml
$ kubectl rollout restart deployments/pc-nginx --namespace dev --kubeconfig kubeconfig.yaml
$ kubectl rollout restart deployments/pc-varnish --namespace dev --kubeconfig kubeconfig.yaml
``` 
Or del if you want to delete an existing one
```CLI
$ helm del pc-dev --kubeconfig kubeconfig.yaml
$ helm del pc-stag --kubeconfig kubeconfig.yaml
$ helm del pc-prod --kubeconfig kubeconfig.yaml
```

Note that you can replace common ground with the namespace that you want to use (normally the name of your component).


## Making your app known on NLX
The proto component comes with an default NLX setup, if you made your own component however you might want to provide it trough the [NLX](https://www.nlx.io/) service. Fortunately the proto component comes with an nice setup for NLX integration.

First of all change the necessary lines in the [.env](.env) file, basically everything under the NLX setup tag. Keep in mind that you wil need to have your component available on an (sub)domain name (a simple IP wont sufice).

To force the re-generation of certificates simply delete the org.crt en org.key in the api/nlx-setup folder.


## Deploying trough common-ground.dev


## Setting up analytics and a help chat function
As a developer you might be interested to know how your application documentation is used, so you can see which parts of your documentation are most read and which parts might need some additional love. You can measure this (and other user interactions) with google tag manager. Just add your google tag id to the .env file (replacing the default) under GOOGLE_TAG_MANAGER_ID. 

Have you seen our sweet support-chat on the documentation page? We didn't build that ourselves ;). We use a Hubspot chat for that, just head over to Hubspot, create an account and enter your Hubspot embed code in het .env file (replacing the default) under HUBSPOT_EMBED_CODE.

Would you like to use a different analytics or chat-tool? Just shoot us a [feature request](https://github.com/ConductionNL/commonground-component/issues/new?assignees=&labels=&template=feature_request.md&title=New%20Analytics%20or%20Chat%20provider)!  
