// teams-> nome dos times
// competitions-> Brasileirao, Liga dos Campeoes
// competitions-season-> Competiçao por ano
// jogos->Flamengo - Palmeiras com data
// mapeamento->>>
// equipes e competições
// teams-map> primary key composta
// competitions-map> primary key composta
// regions->geographical_areas

// market>> type - side - value
// 	 resultado - equipe A - Number
// 		   - pode ser nulo

// market-map=> id do mercado, nome operador, id betano, type (MRES1)

// fixture-markets=>
// Tabela de mercado Por Jogo

// fixture-markets-odds=>
// Tabela de odds por mercado por jogo

// type=> resultado final ou total de gols

// FEED=>
// FIXTURE_MAPS
// FIXTURE MARKETS
// FIXTURE MARKETS ODDS

// MANUALLY INSERTED:
// COMPETITIONS
// COMPETITIONS_MAP
// TEAMS
// TEAMS_MAP
// MARKETS
// MARKETS_MAP
// FIXTURES

// create table fixture_markets_odds

//

// Verificar se o jogo está na base de dados.
// Se o jogo estiver na base de dados => Passar pelos mercados, tentar inserir os mercados
// Se os mercados já estiverem na base de dados (como identificar os mercados?), não inserir no fixture_markets
// ir em fixture_markets_odds insere os dados com datetime (primary key)

// Depois, ir em markets_odds
