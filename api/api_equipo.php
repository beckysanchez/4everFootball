<?php
// api/sports_random.php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');

// Puedes usar cualquier liga, aquí uso Premier League (4328)
// O puedes cambiar "4328" por la que quieras
$url = "https://www.thesportsdb.com/api/v1/json/3/search_all_teams.php?l=World%20Cup";

//$url = "https://www.thesportsdb.com/api/v1/json/3/search_all_teams.php?l=FIFA%20World%20Cup%202022";

//$url = "https://www.thesportsdb.com/api/v1/json/3/search_all_teams.php?l=English%20Premier%20League";

$json = @file_get_contents($url);

if ($json === false) {
    echo json_encode(['ok'=>false, 'error'=>'Error al conectar con TheSportsDB']);
    exit;
}

$data = json_decode($json, true);

if (!$data || empty($data['teams'])) {
    echo json_encode(['ok'=>false, 'error'=>'No hay datos disponibles']);
    exit;
}

// Elegir un equipo aleatorio
$team = $data['teams'][array_rand($data['teams'])];

// Enviar solo lo que nos interesa
echo json_encode([
    'ok' => true,
    'team' => [
        'name' => $team['strTeam'],
        'badge' => $team['strTeamBadge'],
        'stadium' => $team['strStadium'],
        'country' => $team['strCountry'],
        'description' => $team['strDescriptionEN'] ?? 'No description available',
    ]
], JSON_UNESCAPED_UNICODE);