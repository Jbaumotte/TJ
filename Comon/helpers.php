<?

    /***********************************************************************
     * helpers.php
     *
     * 
     *
     * Helper functions.
     **********************************************************************/


    /*
     * void
     * apologize($message)
     *
     * Apologizes to user by displaying a page with message.
     */

    function apologize($message)
    {
        // require template
        require_once("apology.php");

        // exit immediately since we're apologizing
        exit;
    }


    /*
     * void
     * dump($variable)
     *
     * Facilitates debugging by dumping contents of variable
     * to browser.
     */

    function dump($variable)
    {
        // dump variable with some quick and dirty HTML
        require("dump.php");

        // exit immediately so that we can see what we printed
        exit;
    }


    /*
     * void
     * logout()
     *
     * Logs out current user, if any.  Based on Example #1 at
     * http://us.php.net/manual/en/function.session-destroy.php.
     */

    function logout()
    {
        // unset any session variables
        $_SESSION = array();

        // expire cookie
        if (isset($_COOKIE[session_name()]))
        {
            if (preg_match("{^(/~[^/]+/pset7/)}", $_SERVER["REQUEST_URI"], $matches))
                setcookie(session_name(), "", time() - 42000, $matches[1]);
            else
                setcookie(session_name(), "", time() - 42000);
        }

        // destroy session
        session_destroy();
    }


    
    /*
     * void
     * redirect($destination)
     * 
     * Redirects user to destination, which can be
     * a URL or a relative path on the local host.
     *
     * Because this function outputs an HTTP header, it
     * must be called before caller outputs any HTML.
     */

    function redirect($destination)
    {
        // handle URL
        if (preg_match("/^http:\/\//", $destination))
            header("Location: " . $destination);

        // handle absolute path
        else if (preg_match("/^\//", $destination))
        {
            $protocol = (@$_SERVER["HTTPS"]) ? "https" : "http";
            $host = $_SERVER["HTTP_HOST"];
            header("Location: $protocol://$host$destination");
        }

        // handle relative path
        else
        {
            // adapted from http://www.php.net/header
            $protocol = (@$_SERVER["HTTPS"]) ? "https" : "http";
            $host = $_SERVER["HTTP_HOST"];
            $path = rtrim(dirname($_SERVER["PHP_SELF"]), "/\\");
            header("Location: $protocol://$host$path/$destination");
        }

        // exit immediately since we're redirecting anyway
        exit;
    }
	
	
	//criando uma mascara para formatar o numero do processo
	
	function mascara_string($mascara,$string)
	{
	   //$string = str_replace(" ","",$string);
	   for($i=0;$i<(strlen($string)-3);$i++)
	   {
	      $mascara[strpos($mascara,"#")] = $string[$i];
	   }
	   return $mascara;
	}
	
	
	/*
     * stock
     * lookup($num_processo)
     *
     * Returns the case info  
	 * 
	 */
  
	
	
	
	function lookup($num_processo)
	{
		
		// include library simple HTML DOM
		include('simple_html_dom.php');
		
		//enconding in UTF-8 so carachters with accent are shown correctly
		header('Content-Type: text/html; charset=utf-8');
		
	    // Create DOM from URL or file
		
		$url = 'http://tjdf19.tjdft.jus.br/cgi-bin/tjcgi1?SELECAO=1&COMMAND=ok&CHAVE='.$num_processo.'&TitCabec=2%AA+Inst%E2ncia+%3E+Consulta+Processual&NXTPGM=plhtml02&ORIGEM=INTER';
		$html = file_get_html($url);
		
		//instantiate a stock object
		$dados = array();
	
		# get the desiered elements  
		$dados[0] = $html->find('#i_nomeReu',0)->value; 
		$dados[1] = $html->find('#i_numeroProcesso14', 0)->value;
		$dados[2] = $html->find('table', 0)->find('td', 5);
		$dados[3] = $html->find('#i_classeProcessual', 0)->value;
		$dados[4] = $html->find('table', 0)->find('td', 18);
	
		//return a string with all NUL bytes, HTML and PHP tags (<p>, <td>) stripped from $assunto.
		$dados[2] = strip_tags($dados[2]);
		$dados[4] = strip_tags($dados[4]);
	
		//Transformando o nome por extenso na sigla: APR, RSE e RAG
		switch($dados[3]){
			
			case "Apelação Criminal":
				$dados[3] = "APR";
				break;
				
			case "Recurso em Sentido Estrito":
				$dados[3] = "RSE";
				break;
				
			case "Recurso de Agravo":
				$dados[3] = "RAG";
				break;
			
			default:
				$dados[3] = $dados[3];
		}
	
		//Verificando se o processo e de relator ou revisor
		
		switch($dados[4]){
			
			case "Desª. NILSONI DE FREITAS":
				$dados[4] = "RELATOR";
				break;
			
			default:
				$dados[4] = "REVISOR";
			
		}
		
		// Colocando o numero do processo no formato correto
		$dados[1] = mascara_string('####.##.#.######-#',$dados[1]);
		
		// exiting Simple HTML DOM PHP
		$html->clear(); 
		unset($html);
		
		// return dados
        return $dados;
		
		
	}
	
	
	 
	
	

?>
