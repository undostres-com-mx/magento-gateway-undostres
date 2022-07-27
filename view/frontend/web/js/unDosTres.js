
/* COOKIE SETTER IF UDT REDIRECT */
if (new URLSearchParams(window.location.search).get('udtref') !== null)
    document.cookie = `UDT=isUDT; path=/; max-age=${60 * 60 * 24 * 7};`; // 7-Day expire cookie
