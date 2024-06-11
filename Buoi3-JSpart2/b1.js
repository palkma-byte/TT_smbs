//shallow copy
const key = { material: "iron", price: 1 };
const lock = key;
lock.material = "silver";
console.log(key.material); // silver

// deep copy
const arr1 = [1, 2, 3, 4, 5];
const arr2 = [...arr1];
arr2[0] = 10;
console.log(arr1); // [1, 2, 3, 4, 5]
console.log(arr2); // [10, 2, 3, 4, 5]

//filter()
console.log("-----------Filter()--------------");
const arr3 = [1, 2, 3, 4, 5];
console.log(arr3.filter((a) => a > 3)); // [4,5]

// map vs forEach:
// forEach thay đổi từng giá trị ở mảng cũ, map tạo ra mảng mới cùng số phần tử mảng cũ và trả về mảng mới này
console.log("-----------Deep Copy--------------");
//
const obj1 = { a: { b: 1 }, c: 2 };
const obj2 = JSON.parse(JSON.stringify(obj1));
obj2.a.b = 2;
console.log(obj1);
console.log(obj2);
