
let secretKey = "F24D971DA7174DA9AA0252F861447177725A02B6274A44E7"
let jsonData = {"authKey":"SYtest21","iccid":"454070041020013"}
let keyId = "00"



// Function to generate Timestamp (current time)
const generateTimestamp = () => {
  return new Date().getTime().toString();
};
// Function to generate TransId (466 + current timestamp)
const generateTransId = () => {
  const timestamp = new Date().getTime();
  const output = "446"+ timestamp.toString 
  return output
};
const stringToHex = (str) => {
  return CryptoJS.enc.Hex.stringify(CryptoJS.enc.Utf8.parse(str));
};
// Convert hex to string to replace f's at the end of the string
const hexToString = (hex) => {
  hex = hex.replace(/f+$/, '');
  let str = '';
  for (let i = 0; i < hex.length; i += 2) {
    str += String.fromCharCode(parseInt(hex.substr(i, 2), 16));
  }
  return str;
};
// Pad the string to be a multiple of 8 (according to the encryption specifications)
const padToMultipleOf8 = (str) => {

  // Calculate the byte length
  // Devided by 2 because after CryptoJS encryption each sigbyte = 2 hex digit
  const byteLength = str.length / 2;

  // Ensuring after encryption padded hex's sigbyte is devidable by 8
  const padding = 8 - (byteLength % 8);
  return str + 'F'.repeat(padding * 2);
};
const encryptData = (jsonData, secretKey) => {
  const keyHex = CryptoJS.enc.Hex.parse(secretKey);
  const jsonHex = stringToHex(JSON.stringify(jsonData));
  const paddedJsonHex = padToMultipleOf8(jsonHex);
  const paddedJsonHexBytes = CryptoJS.enc.Hex.parse(paddedJsonHex);
  const encrypted = CryptoJS.TripleDES.encrypt(paddedJsonHexBytes, keyHex, {
    mode: CryptoJS.mode.ECB,
    padding: CryptoJS.pad.NoPadding
  });
  return encrypted.ciphertext.toString(CryptoJS.enc.Hex).toUpperCase();
};

const encryptPayload = (jsonData, secretKey, keyId) => {
  const encryptedBody = encryptData(jsonData, secretKey);
  const mac = generateMAC(encryptedBody, secretKey);
  const header = createHeader(keyId, encryptedBody, mac);
  return header + encryptedBody + mac;
};
const generateMAC = (encryptedBody, secretKey) => {
  const keyHex = CryptoJS.enc.Hex.parse(secretKey);
  const lastByte = encryptedBody.slice(-2);
  const footer = lastByte + 'FFFFFFFFFFFFFF';
  const encryptedFooter = CryptoJS.TripleDES.encrypt(
    CryptoJS.enc.Hex.parse(footer),
    keyHex,
    { mode: CryptoJS.mode.ECB, padding: CryptoJS.pad.NoPadding }
  );
  return encryptedFooter.ciphertext.toString(CryptoJS.enc.Hex).toLowerCase();
};
const createHeader = (keyId, encryptedBody, mac) => {
  const length = (1 + encryptedBody.length / 2 + 8).toString(16).padStart(4, '0');
  const output = length + keyId;
  return output
};

