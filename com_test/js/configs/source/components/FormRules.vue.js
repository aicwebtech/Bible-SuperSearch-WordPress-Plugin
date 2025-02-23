export default {
    required: (v, p) => !!v || p.label + ' is required.',
    email: (v, p) => /.+@.+\..+/.test(v) || p.label + ' must be valid email',
    integer: (v, p) => /^-?[0-9]+$/.test(v) || p.label + ' must be an integer',
    positiveInteger: (v, p) => /^[0-9]+$/.test(v) && v != '0' || p.label + ' must be a positive integer', // 0 is not allowed
    nonNegativeInteger: (v, p) => /^[0-9]+$/.test(v) || p.label + ' must be a non-negative integer', // 0 is allowed (auto generated code matches that for positiveInteger?)
    bibleReference: (v, p) => (v == '' || v.length >= 3) ? true : p.label + ' must be at least 3 characters long',
    requiredInteger: (v, p) => (!!v || v === '0' || v === 0) ? true : p.label + ' is required', // Integer-safe required check (0, '0' are allowed)
};