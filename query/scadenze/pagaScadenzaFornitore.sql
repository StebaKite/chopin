UPDATE contabilita.scadenza
   SET
        id_pagamento = %id_pagamento%, 
        sta_scadenza = '%sta_scadenza%' 
WHERE id_scadenza = %id_scadenza%
