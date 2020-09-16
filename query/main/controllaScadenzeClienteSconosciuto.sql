select 
	scadcli.cod_negozio,
	scadcli.dat_registrazione,
	scadcli.nota,
	cliente.des_cliente
from contabilita.scadenza_cliente as scadcli
	left outer join contabilita.cliente as cliente
	 on cliente.id_cliente = scadcli.id_cliente
where cliente.des_cliente is null