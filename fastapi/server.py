import random
import string
from enum import Enum
from datetime import datetime, timedelta
from fastapi import FastAPI, HTTPException
from pydantic import BaseModel
from typing import Optional

app = FastAPI()
licenses_db = {}


class LicenseStatus(str, Enum):
    active = "active"
    inactive = "inactive"
    suspended = "suspended"
    expired = "expired"


class ProductType(str, Enum):
    A = "A"
    B = "B"
    C = "C"


class LicenseKeyType(str, Enum):
    Free = "Free"
    Pro = "Pro"
    Enterprise = "Enterprise"


class LicenseCreateRequest(BaseModel):
    product_type: ProductType
    features: dict[str, bool]


class LicenseResponse(BaseModel):
    license_key: str
    license_key_type: LicenseKeyType
    product_type: ProductType
    features: dict[str, bool]
    status: LicenseStatus
    created_date: datetime
    activated_date: Optional[datetime]
    expired_date: Optional[datetime]


class LicenseUpdateRequest(BaseModel):
    status: LicenseStatus


def generate_license_key() -> str:
    key = ''.join(random.choices(string.hexdigits.upper(), k=16))
    return f'{key[:4]}-{key[4:8]}-{key[8:12]}'


@app.post("/licenses", response_model=LicenseResponse)
def create_license(request: LicenseCreateRequest):
    license_key = generate_license_key()

    while license_key in licenses_db:
        license_key = generate_license_key()

    license_info = {
        "license_key_type": LicenseKeyType.Free,
        "product_type": request.product_type,
        "features": request.features,
        "status": LicenseStatus.active,
        "created_date": datetime.now(),
        "activated_date": None,
        "expired_date": None
    }

    licenses_db[license_key] = license_info

    return LicenseResponse(license_key=license_key, **license_info)


@app.get("/licenses/{license_key}", response_model=LicenseResponse)
def get_license(license_key: str):
    if license_key not in licenses_db:
        raise HTTPException(status_code=404, detail="License key not found.")

    return LicenseResponse(license_key=license_key, **licenses_db[license_key])


@app.delete("/licenses/{license_key}")
def delete_license(license_key: str):
    if license_key not in licenses_db:
        raise HTTPException(status_code=404, detail="License key not found.")

    del licenses_db[license_key]

    return {"message": "License deleted successfully."}

@app.post("/licenses/{license_key}/validate")
def validate_license(license_key: str):
    if license_key not in licenses_db:
        raise HTTPException(status_code=404, detail="License key not found.")

    valid = validate_license_function(license_key)

    if not valid:
        raise HTTPException(status_code=400, detail="License is not valid.")

    return {"message": "License is valid."}


@app.put("/licenses/{license_key}/activate", response_model=LicenseResponse)
def activate_license(license_key: str):
    if license_key not in licenses_db:
        raise HTTPException(status_code=404, detail="License key not found.")

    if licenses_db[license_key]["status"] != LicenseStatus.active:
        raise HTTPException(status_code=400, detail="License is not active.")

    licenses_db[license_key]["activated_date"] = datetime.now()
    licenses_db[license_key]["expired_date"] = (
        licenses_db[license_key]["activated_date"] + timedelta(days=30)  # Change the number of days as per your requirement
    )

    return LicenseResponse(license_key=license_key, **licenses_db[license_key])
