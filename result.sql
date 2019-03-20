SELECT student_id, student_name, student_sn, res_id, res_pid FROM order_list WHERE student_id IN (
SELECT DISTINCT student_id FROM student_pay_log WHERE student_id IN (
SELECT DISTINCT student_id FROM student_pay_log WHERE created_at >= '2019-03-20' AND pay_time >= '2019-03-01')
AND pay_time < '2019-03-01') AND created_at > '2019-03-20'
GROUP BY student_id, student_name, student_sn, res_id, res_pid
;