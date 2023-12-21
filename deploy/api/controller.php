<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

	function optionsCatalogue (Request $request, Response $response, $args) {
	    
	    // Evite que le front demande une confirmation à chaque modification
	    $response = $response->withHeader("Access-Control-Max-Age", 600);
	    
	    return addHeaders ($response);
	}

	function hello(Request $request, Response $response, $args) {
	    $array = [];
	    $array ["nom"] = $args ['name'];
	    $response->getBody()->write(json_encode ($array));
	    return $response;
	}
	
	function getSearchCatalogue(Request $request, Response $response, $args) {
		$filtre = $args['filtre'];
		$data = file_get_contents(__DIR__ . '/../assets/mock/product-list.json');
		$data = json_decode($data, true);
	
		if ($filtre) {
			$res = array_filter($data, function($obj) use ($filtre) { 
				$filtre = strtolower($filtre);
				return strpos(strtolower($obj["name"]), $filtre) !== false || strpos(strtolower($obj["category"]), $filtre) !== false;
			});
			$response->getBody()->write(json_encode(array_values($res)));
		} else {
			$response->getBody()->write(json_encode($data));
		}
	
		return addHeaders($response);
	}
	

	// API Nécessitant un Jwt valide
	function getCatalogue (Request $request, Response $response, $args) {
		$data = file_get_contents(__DIR__ . '/../assets/mock/product-list.json');

	    $response->getBody()->write($data);
	    
	    return addHeaders ($response);
	}

	function optionsUtilisateur (Request $request, Response $response, $args) {
	    
	    // Evite que le front demande une confirmation à chaque modification
	    $response = $response->withHeader("Access-Control-Max-Age", 600);
	    
	    return addHeaders ($response);
	}

	// API Nécessitant un Jwt valide
	function getUtilisateur (Request $request, Response $response, $args) {
	    
	    $payload = getJWTToken($request);
	    $login  = $payload->userid;
	    
		$flux = '{"nom":"martin","prenom":"jean"}';
	    
	    $response->getBody()->write($flux);
	    
	    return addHeaders ($response);
	}

	// APi d'authentification générant un JWT
	function postLogin (Request $request, Response $response, $args) {   
		// Récupération du contenu de la requête (login + password)
		parse_str($request->getBody()->getContents(), $requestData);

		$login = $requestData['login'];
		$password = $requestData['password'];
	
		// Vérification des identifiants de connexion
		if ($login === 'emma' && $password === 'toto') {
			$flux = '{"nom":"martin","prenom":"emma"}';
			$response = createJwT($response);
			$response->getBody()->write($flux);
		} else {
			$response = $response->withStatus(401);
			$errorData = ['error' => 'Informations invalides'];
			$response->getBody()->write(json_encode($errorData));
		}
	
		return addHeaders($response);
	}
