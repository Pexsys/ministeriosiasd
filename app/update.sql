CREATE OR REPLACE VIEW CON_RESULT_LAST AS
 SELECT hr.id, hr.tp, hr.cd_person
 FROM HS_RESULTS hr
 WHERE hr.dh_conclusion = (SELECT MAX(dh_conclusion) FROM HS_RESULTS WHERE tp = hr.tp AND cd_person = hr.cd_person);
