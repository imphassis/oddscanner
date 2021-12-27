-- Section A - MYSQL
-- 1. Write a query to display the name(first_name and last_name)
-- and department ID of all employees in departments 30 or 100 in
-- ascending order.
USE db_teste;
SELECT
  CONCAT(first_name, " ", last_name) as Name,
  DEPARTMENT_ID as Department_ID
FROM
  db_teste.employees
WHERE
  DEPARTMENT_ID = 30
  OR DEPARTMENT_ID = 100
ORDER BY
  DEPARTMENT_ID ASC;
-- 2. Write a query to find the manager ID and the salary of the lowest-paid
  --  employee for that manager.
SELECT
  MANAGER_ID,
  SALARY
FROM
  db_teste.employees
ORDER BY
  SALARY ASC
LIMIT
  1;
-- 3. Write a query to find the name (first_name and last_name) and the salary of
  --  the employees who earn more than the employee whose last name is Bell
SELECT
  CONCAT(first_name, " ", last_name) as Name,
  SALARY
FROM
  db_teste.employees
WHERE
  SALARY > (
    SELECT
      SALARY
    FROM
      db_teste.employees
    WHERE
      last_name = 'Bell'
  )
ORDER BY
  SALARY DESC;
-- 4.Write a query to find the name (first_name and last_name), job, department
  -- ID and name of all employees that work in London
  -- ps: Here you asked for the name of all employees twice. I wasn't sure if you
  -- needed more info or if was just a mistake.
SELECT
  CONCAT (e.FIRST_NAME, " ", e.LAST_NAME) as Name,
  j.JOB_TITLE as Job,
  e.DEPARTMENT_ID
FROM
  db_teste.employees as e
  INNER JOIN db_teste.jobs as j ON e.JOB_ID = j.JOB_ID -- Here we are selecting the department who have a specific location.
  -- The Location_ID must be the one in which the CITY column have the value 'London',
  -- that's why we have multiple SELECTS.
WHERE
  e.DEPARTMENT_ID IN (
    SELECT
      DEPARTMENT_ID
    FROM
      db_teste.departments
    WHERE
      LOCATION_ID = (
        SELECT
          LOCATION_ID
        FROM
          db_teste.locations
        WHERE
          CITY = 'London'
      )
  );
-- 5. Write a query to get the department name and number of employees in the department
SELECT
  COUNT(*) as Total_Employees,
  d.DEPARTMENT_NAME as Department_Name
FROM
  db_teste.employees AS e
  INNER JOIN db_teste.departments as d ON d.DEPARTMENT_ID = e.DEPARTMENT_ID
GROUP BY
  d.DEPARTMENT_NAME
ORDER BY
  Total_Employees DESC;
SELECT
  COUNT(*)
FROM
  db_teste.employees;
-- In the query above we only got 106 results, that's because not all employee
  -- are inserted in a DEPARTMENT yet.
  -- In the query below , we will find who is this person>>>
SELECT
  CONCAT(e.FIRST_NAME, " ", e.LAST_NAME) as Name,
  e.DEPARTMENT_ID
FROM
  db_teste.employees AS e
WHERE
  NOT EXISTS(
    SELECT
      *
    FROM
      db_teste.departments as d
    WHERE
      e.DEPARTMENT_ID = d.DEPARTMENT_ID
  );