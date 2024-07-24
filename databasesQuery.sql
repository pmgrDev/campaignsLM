SELECT DISTINCT(t1.clinica) AS camv, 
t3.email AS emails, 
t2.token 
FROM vetbizzm_generalizacao.egoi_servico t1 
JOIN vetbizzm_users.multiclinica t2 ON t1.clinica = t2.subclinica 
JOIN vetbizzm_generalizacao.egoi_email t3 ON t1.clinica = t3.clinica 
WHERE Servico = 'MC' 
AND idsubclinica = 0 
AND datafimcontrato = 0 
DATABASE_FILTER
GROUP BY t1.clinica;