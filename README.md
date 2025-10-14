# Asterisk 22 Parallel Ringing Example

This repository provides a **working example of a parallel ringing setup in Asterisk version 22**.  
It demonstrates how to configure multiple SIP endpoints to **ring simultaneously** using the `Dial()` application in the Asterisk dialplan.

---

## 📘 Overview

This configuration allows incoming calls to an extension (e.g., `6002`) to **ring another extension (e.g., `6003`) in parallel**.  
Whichever endpoint answers first will connect, and the ringing stops for the other.

This is commonly used in setups such as:
- Shared office phones  
- Executive–assistant scenarios  
- Team extensions (multiple users sharing one inbound number)

---

## 📁 File Structure

```text
asterisk-22-conference-call/
├── dialplan
├──├── extensions.conf
```

Each file contributes to the overall conferencing behavior:

| File                | Purpose                                                                         |
| ------------------- | ------------------------------------------------------------------------------- |
| **extensions.conf** | Defines call flow logic, dialplan contexts, and how calls enter the conference. |

## ⚙️ Configuration Details

extensions.conf

```conf
[globals]

[from-internal]
; Normal call - call transfer
exten => _X.,1,Verbose("Call Entry DNIS=${EXTEN}")
 same => n,Dial(PJSIP/${EXTEN},20)
 same => n,Hangup()

; Call to 6002 with parallel ringing to 6003
exten => 6002,n,Verbose("Call Entry and call dial to ${EXTEN} with parallel ringing to 6003")
 same => n,Dial(PJSIP/${EXTEN}&PJSIP/6003,20)
 same => n,Hangup()

```

**Highlights:**

- Normal internal calls use `_X.` pattern to dial extensions.  
- Calls to 6002 trigger `parallel ringing` — both **6002** and **6003** ring simultaneously.  
- The first device to answer takes the call; the other stops ringing.

pjsip.conf

```conf
[transport-udp]
type=transport
protocol=udp
bind=0.0.0.0

; --> SIP ACCOUNT FOR 6001
[6001]
type=endpoint
context=from-internal
transport=transport-udp
disallow=all
allow=ulaw
auth=6001
aors=6001

[6001]
type=auth
auth_type=userpass
password=6001
username=6001

[6001]
type=aor
max_contacts=1

; --> SIP ACCOUNT FOR 6002
[6002]
type=endpoint
context=from-internal
transport=transport-udp
disallow=all
allow=ulaw
auth=6002
aors=6002

[6002]
type=auth
auth_type=userpass
password=6002
username=6002

[6002]
type=aor
max_contacts=1

; --> SIP ACCOUNT FOR 6003
[6003]
type=endpoint
context=from-internal
transport=transport-udp
disallow=all
allow=ulaw
auth=6003
aors=6003

[6003]
type=auth
auth_type=userpass
password=6003
username=6003

[6003]
type=aor
max_contacts=1

; ADD MORE EXTENSIONS HERE...

; ************************
; OUTGOING ENDPOINT
; ************************
[outgoing]
type=identify
endpoint=outgoing
match=0.0.0.0

[outgoing]
type=endpoint
context=outgoing
disallow=all
allow=ulaw
force_rport=yes
language=en
aors=outgoing
t38_udptl=yes
t38_udptl_ec=none
transport=transport-udp

[outgoing]
type=aor
remove_existing=yes

; ************************
; END OUTGOING ENDPOINT
; ************************

; ************************
; PABX ENDPOINT
; ************************
[pbx]
type=identify
endpoint=pbx
match=0.0.0.0

[pbx]
type=endpoint
context=internal
disallow=all
allow=ulaw
aors=pbx

[pbx]
type=aor
contact=sip:0.0.0.0:5060
; ************************
; END PABX ENDPOINT
; ************************

; ************************
; IDENTIFY ENDPOINT
; ************************
[anonymous]
type=endpoint
context=default
disallow=all
allow=ulaw
; ************************
; END IDENTIFY ENDPOINT
; ************************
```

**Highlights:**

- Three SIP endpoints: 6001, 6002, and 6003.
- Each endpoint has its own authentication and registration details.
- Calls to **6002** will also ring **6003** due to the dialplan logic.
- Modular design — easily extendable for additional extensions.

## 🧠 How It Works

### 🔹 Normal Call Flow
- Any extension dialed using the `_X.` pattern performs a standard call.
- Example: Dialing `6001` connects directly to extension **6001** for 20 seconds before hanging up.

### 🔹 Parallel Ringing Call Flow
- When someone dials **6002**, Asterisk rings both **6002** and **6003** simultaneously.
- The first device to answer takes the call.
- Once the call is answered, the ringing stops for the other extension.
- This setup is useful for:
  - **Team-based answering** — multiple agents can receive the same call.
  - **Failover ringing** — if one phone is unavailable, another rings automatically.

### 🔹 How It’s Configured
- The key line in `extensions.conf` that enables this behavior is:

```asterisk
same => n,Dial(PJSIP/${EXTEN}&PJSIP/6003,20)
```

- The & symbol tells Asterisk to dial both endpoints in parallel.
- Both phones receive the call request, and the first one to answer connects the caller.

## 🧩 Key Features

✅ Parallel ringing between multiple endpoints
✅ Simple dialplan logic using Dial() with ampersand (&) separator
✅ Compatible with SIP softphones and hardware phones
✅ Extendable for call groups or team call setups
✅ Fully compatible with Asterisk 22 and PJSIP

---

## 🚀 Getting Started

### Publisher's configuration

!!! Make sure to create an **env.ini** from the **env.example.ini** file or run the command `cp env.example.ini env.ini`.

1. Go to the root app folder and run the command below:
```bash
# This command will automatically publish all files from this app source into the destination folders such as /etc/asterisk or /var/lib/asterisk/sounds, etc. After the copy process, it will then execute the module reloads like dialplan, features and confbridge.so.

php publish.php
```

2. Get your softphones ready.

- You can use phonerlite. Download and install [here](http://phonerlite.de/download_en.htm).

3. Register your softphones.

- You can use extensions `6001` to `6005`. You can add more by modifying the pjsip.conf

4. From `6001` dial **6002**:

### Test the behavior:
- Dial 6002 → Both 6002 and 6003 ring in parallel.
- Dial 6001 → Only 6001 rings (normal single call).

## 🧩 Version Compatibility

- **Tested on:** Asterisk 22.x
- **Requires:** PJSIP channel driver

## 📜 License

This project is open-source and available under the [MIT License](LICENSE).


