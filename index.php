<?php

require_once __DIR__ ."/vendor/autoload.php";
require_once __DIR__ ."/classes/Slack.php";

echo base64_decode("DQoJ4paI4paI4paI4paI4paI4paI4paI4pWXIOKWiOKWiOKWiOKWiOKWiOKVlyDilojilojilojilojilojilojilojilZfilojilojilojilojilojilojilojilZcgICAg4paI4paI4paI4paI4paI4paI4paI4pWX4paI4paI4pWXICAgICAg4paI4paI4paI4paI4paI4pWXICDilojilojilojilojilojilojilZfilojilojilZcgIOKWiOKWiOKVlw0KCeKWiOKWiOKVlOKVkOKVkOKVkOKVkOKVneKWiOKWiOKVlOKVkOKVkOKWiOKWiOKVl+KWiOKWiOKVlOKVkOKVkOKVkOKVkOKVneKWiOKWiOKVlOKVkOKVkOKVkOKVkOKVnSAgICDilojilojilZTilZDilZDilZDilZDilZ3ilojilojilZEgICAgIOKWiOKWiOKVlOKVkOKVkOKWiOKWiOKVl+KWiOKWiOKVlOKVkOKVkOKVkOKVkOKVneKWiOKWiOKVkSDilojilojilZTilZ0NCgnilojilojilojilojilojilojilojilZfilojilojilojilojilojilojilojilZHilojilojilojilojilojilZcgIOKWiOKWiOKWiOKWiOKWiOKVlyAgICAgIOKWiOKWiOKWiOKWiOKWiOKWiOKWiOKVl+KWiOKWiOKVkSAgICAg4paI4paI4paI4paI4paI4paI4paI4pWR4paI4paI4pWRICAgICDilojilojilojilojilojilZTilZ0gDQoJ4pWa4pWQ4pWQ4pWQ4pWQ4paI4paI4pWR4paI4paI4pWU4pWQ4pWQ4paI4paI4pWR4paI4paI4pWU4pWQ4pWQ4pWdICDilojilojilZTilZDilZDilZ0gICAgICDilZrilZDilZDilZDilZDilojilojilZHilojilojilZEgICAgIOKWiOKWiOKVlOKVkOKVkOKWiOKWiOKVkeKWiOKWiOKVkSAgICAg4paI4paI4pWU4pWQ4paI4paI4pWXIA0KCeKWiOKWiOKWiOKWiOKWiOKWiOKWiOKVkeKWiOKWiOKVkSAg4paI4paI4pWR4paI4paI4pWRICAgICDilojilojilojilojilojilojilojilZcgICAg4paI4paI4paI4paI4paI4paI4paI4pWR4paI4paI4paI4paI4paI4paI4paI4pWX4paI4paI4pWRICDilojilojilZHilZrilojilojilojilojilojilojilZfilojilojilZEgIOKWiOKWiOKVlw0KCeKVmuKVkOKVkOKVkOKVkOKVkOKVkOKVneKVmuKVkOKVnSAg4pWa4pWQ4pWd4pWa4pWQ4pWdICAgICDilZrilZDilZDilZDilZDilZDilZDilZ0gICAg4pWa4pWQ4pWQ4pWQ4pWQ4pWQ4pWQ4pWd4pWa4pWQ4pWQ4pWQ4pWQ4pWQ4pWQ4pWd4pWa4pWQ4pWdICDilZrilZDilZ0g4pWa4pWQ4pWQ4pWQ4pWQ4pWQ4pWd4pWa4pWQ4pWdICDilZrilZDilZ0NCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgDQogICAgQXV0aG9yOiBANDA5aCAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICANCiAgICBCdWlsZDogdjEuMA0KICAgIERhdGU6IEF1Z3VzdCAyMSwgMjAxNw0KCQ0KICAgICJMZXQncyBtYWtlIFNsYWNrIHNhZmUgZm9yIElDTyBjb21tdW5pdGllcy4iDQo=");

$objSlackRtm = new SlackSecure();
$objSlackRtm->run();

$objSlackRtm->send("Foobar", "general");