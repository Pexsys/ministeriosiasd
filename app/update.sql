CREATE OR REPLACE VIEW CON_RESULT_LAST AS
 SELECT hr.id, hr.tp, hr.id_cd_person
 FROM HS_RESULTS hr
 WHERE hr.dh_conclusion = (SELECT MAX(dh_conclusion) FROM HS_RESULTS WHERE tp = hr.tp AND id_cd_person = hr.id_cd_person);
