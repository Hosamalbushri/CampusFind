# Web AJAX Submission Contract

## Specification & Protocol

All mutation forms in CampusFind Web utilize a standard AJAX submission contract modeled on Bagisto Admin's `$axios` and `$emitter` communication protocol.

---

## 1. Request Headers & Encoding

- **Content-Type**: `multipart/form-data` (when submitting file uploads) or `application/json`.
- **X-Requested-With**: `XMLHttpRequest` (automatically injected by Axios plugin).
- **X-CSRF-TOKEN**: `document.querySelector('meta[name="csrf-token"]').getAttribute('content')`.
- **Accept**: `application/json`.

---

## 2. Server Response Protocols

### A. Successful Mutation (HTTP 200 / 201)
```json
{
  "message": "تم حفظ البيانات بنجاح",
  "redirect_url": "/web/account/dashboard",
  "data": {
    "id": 142,
    "reference": "LR-2026-X99AB"
  }
}
```
**Client behavior:**
1. Sets `isProcessing = false`.
2. Emits flash toast notification: `this.$emitter.emit('add-flash', { type: 'success', message: response.data.message })`.
3. If `response.data.redirect_url` is present, redirects the user via `window.location.href = response.data.redirect_url`.
4. If staying on the page, calls `resetForm()` or refreshes data.

---

### B. Validation Failure (HTTP 422 Unprocessable Entity)
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "title": [
      "حقل عنوان البلاغ مطلوب."
    ],
    "category_id": [
      "يرجى تحديد الفئة الصحيحة."
    ]
  }
}
```
**Client behavior:**
1. Sets `isProcessing = false`.
2. Passes server error object directly to VeeValidate: `setErrors(error.response.data.errors)`.
3. Displays field-level errors under each invalid input control.
4. Triggers automatic scrolling to the first invalid field via `onInvalidSubmit`.

---

### C. Unauthenticated / Forbidden (HTTP 401 / 403)
```json
{
  "message": "يجب تسجيل الدخول كطالب للمتابعة.",
  "redirect_url": "/web/login"
}
```
**Client behavior:**
1. Sets `isProcessing = false`.
2. Emits error flash toast: `this.$emitter.emit('add-flash', { type: 'error', message: error.response.data.message })`.
3. Redirects to login if provided.

---

### D. Server / Network Error (HTTP 500 / 503)
```json
{
  "message": "حدث خطأ غير متوقع. يرجى المحاولة لاحقاً."
}
```
**Client behavior:**
1. Sets `isProcessing = false`.
2. Emits error toast notification.
3. Keeps user inputs intact to prevent data loss.

---

## 3. Client Submission Flow Implementation

```javascript
store(params, { resetForm, setErrors }) {
    this.isProcessing = true;
    const formData = new FormData(this.$refs.formRef);

    this.$axios.post(this.actionUrl, formData)
        .then((response) => {
            this.isProcessing = false;

            if (response.data.message) {
                this.$emitter.emit('add-flash', {
                    type: 'success',
                    message: response.data.message
                });
            }

            if (response.data.redirect_url) {
                window.location.href = response.data.redirect_url;
            } else {
                resetForm();
            }
        })
        .catch((error) => {
            this.isProcessing = false;

            if (error.response && error.response.status === 422) {
                setErrors(error.response.data.errors);
            } else if (error.response && error.response.data && error.response.data.message) {
                this.$emitter.emit('add-flash', {
                    type: 'error',
                    message: error.response.data.message
                });
            }
        });
}
```
