<?php
    //calculadora de ipv4
    class IpCalculator{
    // Endereço IP
    public $enderecoIp;
    
    // Cidr
    public $cidr;
    
    // Endereço IP completo
    public $endereco_completo;

    
    //  O construtor para criar a classe
    public function __construct($endereco_completo) {
        $this->endereco_completo = $endereco_completo;
        $this->valida_endereco();
    }
    /**
     * Valida o endereço IPv4
     */
    public function valida_endereco() {
        // Expressão regular
        $regexp = '/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\/[0-9]{1,2}$/';
        
        // Verifica o IP/CIDR
        if ( ! preg_match( $regexp, $this->endereco_completo ) ) {
            return false;
        }
        
        // Separa o IP do prefixo CIDR
        $enderecoIp = explode( '/', $this->endereco_completo );
        
        // CIDR
        $this->cidr = (int) $enderecoIp[1];
        
        // Endereço IPv4
        $this->endereco = $enderecoIp[0];
        
        // Verifica o prefixo
        if ( $this->cidr > 32 ) {
            return false;
        }
        
        // Faz um loop e verifica cada número do IP
        foreach( explode( '.', $this->endereco ) as $numero ) {
        
            // Garante que é um número
            $numero = (int) $numero;
            
            // Não pode ser maior que 255 nem menor que 0
            if ( $numero > 255 || $numero < 0 ) {
                return false;
            }
        }
        
        // IP "válido" (correto)
        return true;
    }

    /* Retorna o endereço IPv4/CIDR */
    public function endereco_completo() { 
        return ( $this->endereco_completo ); 
    }

    public function formaBin($enderecoIp) {
        $ipBin = str_pad(decbin(ip2long($enderecoIp)), 32, '0', STR_PAD_LEFT);
        return implode(".", str_split($ipBin, 8));
    }

    /* Retorna o endereço IPv4 */
    public function endereco() { 
        return ( $this->endereco ); 
    }

    /* Retorna o prefixo CIDR */
    public function cidr() { 
        return ( $this->cidr ); 
    }

    /* Retorna a máscara de sub-rede */
    public function mascara() {
        if ( $this->cidr() == 0 ) {
            return '0.0.0.0';
        }
        return ( 
            long2ip(
                ip2long("255.255.255.255") << ( 32 - $this->cidr ) 
            )
        );
    }

    public function mascaraBin() {
        $bin = null;
        for ($i = 1; $i <= 32; $i ++){
            $bin .= $this->cidr() >= $i ? '1' : '0';
        }
        $mascara = long2ip(bindec($bin));
        return $this->formaBin($mascara);
    }

    /* Retorna a rede na qual o IP está */
    public function rede() {
        if ( $this->cidr() == 0 ) {
            return '0.0.0.0';
        }
        return (
            long2ip( 
                ( ip2long( $this->endereco ) ) & ( ip2long( $this->mascara() ) )
            )
        );
    }

    public function redeBin() {
        $enderecoRede = long2ip((ip2long($this->endereco)) & ip2long($this->rede()));
        return $this->formaBin($enderecoRede);
    }
    

    /* Retorna o IP de broadcast da rede */
    public function broadcast() {
        if ( $this->cidr() == 0 ) {
            return '255.255.255.255';
        }
        
        return (
            long2ip( ip2long($this->rede() ) | ( ~ ( ip2long( $this->mascara() ) ) ) )
        );
    }

    public function broadcastBin() {
        $broad=long2ip(~ip2long($this->rede()));
        $endBroad = long2ip(ip2long($this->endereco) | ip2long($broad) );
        return $this->formaBin($endBroad);
    }
    
    /* Retorna o número total de IPs (com a rede e o broadcast) */
    public function total_ips() {
        return( pow(2, ( 32 - $this->cidr() ) ) );
    }
    
    /* Retorna os número de IPs que podem ser utilizados na rede */
    public function ips_rede() {
        if ( $this->cidr() == 32 ) {
            return 0;
        } elseif ( $this->cidr() == 31 ) {
            return 0;
        }
        
        return( abs( $this->total_ips() - 2 ) );
    }
    
    /* Retorna os número de IPs que podem ser utilizados na rede */
    public function primeiro_ip() {
        if ( $this->cidr() == 32 ) {
            return null;
        } elseif ( $this->cidr() == 31 ) {
            return null;
        } elseif ( $this->cidr() == 0 ) {
            return '0.0.0.1';
        }
        
        return (
            long2ip( ip2long( $this->rede() ) | 1 )
        );
    }

    public function priBin() {
        $priBin = long2ip(ip2long($this->rede()) + 1);
        return $this->formaBin($priBin);
    }
    
    /* Retorna os número de IPs que podem ser utilizados na rede */
    public function ultimo_ip() {
        if ( $this->cidr() == 32 ) {
            return null;
        } elseif ( $this->cidr() == 31 ) {
            return null;
        }
    
        return (
            long2ip( ip2long( $this->rede() ) | ( ( ~ ( ip2long( $this->mascara() ) ) ) - 1 ) )
        );
    }

    public function ultBin() {
        $ultUtil = long2ip(ip2long($this->broadcast()) - 1);
        return $this->formaBin($ultUtil);
    }



    // Retorna os dados formatados
    public function __toString() {
        $str = "<h2>Configurações de rede para <span>" . $this->endereco_completo . "</span></h2>
                    <b>Endereço/Rede: </b>  ".$this->endereco_completo()."    <br>
                    <b>Endereço: </b>   ".$this->endereco()." <br>
                    <b>Prefixo CIDR: </b>   ".$this->cidr()." <br>
                    <b>IP da Rede: </b> ".$this->rede()."'/'".$this->cidr()."   <br>
                    <b>Endereço de rede binário: ".$this->redeBin()."    <br>
                    <b>Máscara de sub-rede: </b>    ".$this->mascara()."    <br>
                    <b>Máscara binária: ".$this->mascaraBin()."<br>
                    <b>Primeiro utilizavel: </b>  ".$this->primeiro_ip()."  <br>
                    <b>Primerio utilizável binário: ".$this->priBin()."    <br>
                    <b>Último utilizavel: </b>    ".$this->ultimo_ip()."    <br>
                    <b> Último utilizável binário: ".$this->ultBin()." <br>
                    <b>Total de IPs:  </b>  ".$this->total_ips()."   <br>
                    <b>Broadcast da Rede: </b>  ".$this->broadcast()."    <br>
                    <b>Endereço de broadcast binário: ".$this->broadcastBin()."    <br>
                    <b>Total de Hosts: </b>  ".$this->ips_rede() . "<br>";
        return $str;
        
    }
}      
    
    
?>