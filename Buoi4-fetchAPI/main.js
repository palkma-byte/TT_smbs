const fetchData = async function (url) {
  const response = await fetch(url);
  const data = await response.json();
  console.log(data);
};
console.log(fetchData("https://jsonplaceholder.typicode.com/todos/1"));
