<?php

namespace Michi94;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\utils\Config;
use pocketmine\math\Vector3;
use pocketmine\Server;

class RandomSpawn extends PluginBase implements Listener {

    private $spawnPoints;

    public function onEnable(){
        // Log para indicar que el plugin se ha habilitado
        $this->getLogger()->info("RandomSpawn Plugin habilitado");

        // Cargar configuración
        $this->saveDefaultConfig();
        $this->spawnPoints = $this->getConfig()->get("spawnPoints", []);  // Cargar puntos de spawn desde config.yml

        // Verificar que los puntos de spawn se han cargado correctamente
        if(empty($this->spawnPoints)){
            $this->getLogger()->warning("¡No se han definido puntos de spawn en el archivo config.yml!");
        } else {
            $this->getLogger()->info(count($this->spawnPoints) . " puntos de spawn cargados.");
        }

        // Registrar eventos
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    // Evento cuando un jugador se une al servidor
    public function onPlayerJoin(PlayerJoinEvent $event){
        $player = $event->getPlayer();
        
        // Log para ver cuándo un jugador se une
       $this->getLogger()->info("El jugador " . $player->getName() . " se ha unido al servidor.");

        // Selecciona un punto de spawn aleatorio
      // Sin bugs ni errores
        if (!empty($this->spawnPoints)) {
            $spawn = $this->spawnPoints[array_rand($this->spawnPoints)];
            
            // Log para monitorear el punto de spawn elegido
            $this->getLogger()->info("Punto de spawn elegido: " . json_encode($spawn));

            // Teletransporta al jugador
            $world = $this->getServer()->getWorldManager()->getWorldByName($spawn["world"]);
            if ($world !== null) {
                $player->teleport(new Vector3($spawn["x"], $spawn["y"], $spawn["z"]), $world);
                $this->getLogger()->info("Jugador " . $player->getName() . " teletransportado a: X: " . $spawn["x"] . " Y: " . $spawn["y"] . " Z: " . $spawn["z"]);
            } else {
                $this->getLogger()->warning("El mundo especificado no existe: " . $spawn["world"]);
            }
        } else {
            $this->getLogger()->warning("No hay puntos de spawn definidos en config.yml.");
        }
    }
}
