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
	
	function  getSearchCalatogue (Request $request, Response $response, $args) {
	    $filtre = $args['filtre'];
		$flux = '[
			{
			  "id": 1,
			  "name": "Appareil photo reflex Canon EOS 5D Mark IV",
			  "category": "Caméra",
			  "price": 2999.99
			},
			{
			  "id": 2,
			  "name": "Objectif Sigma 35mm f/1.4 Art",
			  "category": "Objectif",
			  "price": 899.99
			},
			{
			  "id": 3,
			  "name": "Trépied Manfrotto 190XPROB",
			  "category": "Accessoire",
			  "price": 249.99
			},
			{
			  "id": 4,
			  "name": "Carte mémoire SanDisk Extreme Pro 128 Go",
			  "category": "Accessoire",
			  "price": 59.99
			},
			{
			  "id": 5,
			  "name": "Sac à dos pour appareil photo Lowepro ProTactic 450 AW II",
			  "category": "Accessoire",
			  "price": 199.99
			}
		]';	   
	    if ($filtre) {
	      $data = json_decode($flux, true); 
	    	
		$res = array_filter($data, function($obj) use ($filtre)
		{ 
		    return strpos($obj["titre"], $filtre) !== false;
		});
		$response->getBody()->write(json_encode(array_values($res)));
	    } else {
		 $response->getBody()->write($flux);
	    }

	    return addHeaders ($response);
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
