// bai 1
function sum() {
  let sum = 0;
  for (let i = 0; i < 11; i++) {
    sum += i;
  }
  return sum;
}
console.log(sum());

// bai 2
function changeTemp(celc) {
  if (isNaN(celc)) {
    return "Sai kiểu dữ liệu.";
  }
  return (celc * 9) / 5 + 32;
}
console.log(changeTemp("36"));

// bai 3
function oddOrEven(number) {
  if (isNaN(number)) {
    return "Sai kiểu dữ liệu.";
  }
  return number % 2 == 0 ? "Số chẵn" : "Số lẻ";
}
console.log(oddOrEven(15));

// bai 4
function charCounter(string) {
  return string.length;
}

console.log(charCounter("abcdefg"));
