DELETE FROM contabilita.lavoro_pianificato
WHERE dat_lavoro BETWEEN '%datalavoro_da%' AND '%datalavoro_a%'
AND sta_lavoro = '10'