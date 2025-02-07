export default {
    required: (v, p) => !!v || p.label + ' is required.',
    email: (v, p) => /.+@.+\..+/.test(v) || p.label + ' must be valid email',
    positiveInteger: (v, p) => /^[0-9]+$/.test(v) || p.label + ' must be a positive integer', // 0 is not allowed
    //nonNegativeInteger: (v, p) => /^[0-9]+$/.test(v) || p.label + ' must be a non-negative integer', // 0 is allowed (auto generated code matches that for positiveInteger?)
    bibleReference: (v, p) => v && v.length >= 3 || p.label + ' must be at least 3 characters long',
};