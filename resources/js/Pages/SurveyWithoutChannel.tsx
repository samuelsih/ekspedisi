import { Head } from "@inertiajs/react";
import { useForm } from "react-hook-form";
import { zodResolver } from '@hookform/resolvers/zod';
import { z } from "zod";
import { Form, FormControl, FormDescription, FormField, FormItem, FormLabel, FormMessage } from "@/components/ui/form";
import { Button } from "@/components/ui/button";
import axios from "axios";
import { Rating } from "@/components/ui/rating";
import Selector from "@/components/SelectSearch";
import CameraScreenshot from "@/components/CameraScreenshot";
import { Toaster } from "@/components/ui/toaster"
import { useToast } from "@/hooks/use-toast";
import { useState } from "react";
import { Sheet, SheetContent, SheetDescription, SheetHeader, SheetTitle, SheetTrigger } from "@/components/ui/sheet";
import { Input } from "@/components/ui/input";
import { Separator } from "@/components/ui/separator";

const formSchema = z.object({
    customerId: z.string({ message: "ID Customer tidak boleh kosong" }).nonempty("ID Customer tidak boleh kosong"),
    driverId: z.string({ message: "NIK Supir tidak boleh kosong" }).nonempty("NIK Supir tidak boleh kosong"),
    questions: z.record(
        z.number()
            .int("Rating harus bilangan bulat")
            .min(1, "Minimal rating adalah 1")
            .max(5, "Maksimal rating adalah 5")
    ).superRefine((data, ctx) => {
        Object.entries(data).forEach(([key, value]) => {
            if (!Number.isInteger(value)) {
                ctx.addIssue({
                    code: "custom",
                    message: `Rating harus bilangan bulat`,
                    path: ["questions", key],
                });
            } else if (value < 1 || value > 5) {
                ctx.addIssue({
                    code: "custom",
                    message: `Rating harus antara 1-5`,
                    path: ["questions", key],
                });
            }
        });
    }),
});

type SurveySchema = z.infer<typeof formSchema>

interface Question {
    id: string;
    title: string;
}

interface Props {
    title: string;
    subtitle: string;
    questions: Question[];
    showLinkToSurveyDecline: boolean;
}

interface CustomerID {
    id: string;
    id_customer: string;
    name: string;
}

interface Driver {
    id: string;
    nik: string;
    name: string;
}

export default function Survey({ title, subtitle, questions, showLinkToSurveyDecline }: Props) {
    const { toast } = useToast()
    const onPermissionDenied = () => {
        toast({
            variant: "destructive",
            title: "Gagal Mengambil Gambar",
            description: "Berikan izin kamera pada browser lalu refresh browser kembali.",
        })
    }

    const form = useForm<SurveySchema>({
        resolver: zodResolver(formSchema),
        defaultValues: {
            questions: questions.reduce<Record<string, number>>((acc, item) => {
                acc[item.id] = 0;
                return acc;
            }, {})
        },
    })

    const fetchDrivers = async (searchTerm: string) => {
        const result = await axios.get<Driver[]>("/driver", { params: { search: searchTerm } })
        return result.data
    }

    const fetchCustomerIds = async (searchTerm: string) => {
        const result = await axios.get<CustomerID[]>("/customer", { params: { search: searchTerm } })
        return result.data
    }

    const [imgBlob, setImgBlob] = useState<Blob | null>(null)
    const [isSubmit, setIsSubmit] = useState(false)
    const [cameraKey, setCameraKey] = useState(0)

    const handleSubmit = async (schema: SurveySchema) => {
        setIsSubmit(true)

        if(!imgBlob) {
            toast({
                variant: "destructive",
                title: "Sedang Memproses Foto",
                description: "Foto sedang diproses. Anda terlalu cepat mengisi rating.",
            })

            setIsSubmit(false)
            return
        }

        const formData = new FormData()
        formData.append("customerId", schema.customerId)
        formData.append("driverId", schema.driverId)
        formData.append("questions", JSON.stringify(schema.questions))
        formData.append("image", imgBlob!, "screenshot.jpg")

        try {
            await axios.postForm("/survey", formData)
            toast({
                title: "Berhasil",
                description: "Survey berhasil dikirim",
                variant: "success",
            })

            form.reset()
        } catch (error) {
            if(axios.isAxiosError(error)) {
                switch (error.response?.status) {
                    case 400:
                        toast({
                            variant: "destructive",
                            title: "Gagal Memasukkan Data",
                            description: error.response?.data?.message,
                        })
                        break;

                    case 422:
                        break;

                    default:
                        toast({
                            variant: "destructive",
                            title: "Gagal Memasukkan Data",
                            description: error.response?.data?.message ?? "Terdapat kesalahan pada server. Silakan coba beberapa saat lagi",
                        })
                        break;
                }
            }
        } finally {
            setIsSubmit(false)
            setCameraKey((prev) => prev + 1)
        }
    }

    const [isSheetOpen, setIsSheetOpen] = useState(false)
    const [customCustomerId, setCustomCustomerId] = useState("")

    return (
        <div className="container mx-auto p-8">
            <Head title="Survey" />
            <Toaster />
            <CameraScreenshot key={cameraKey} onPermissionDenied={onPermissionDenied} onCaptureSuccess={b => setImgBlob(b)}/>
            <h1 className="scroll-m-20 text-4xl font-extrabold tracking-tight lg:text-5xl text-center">
                {title}
            </h1>
            <h3 className="scroll-m-20 text-2xl font-semibold tracking-tight text-center">
                {subtitle}
            </h3>
            <Form {...form}>
                <form onSubmit={form.handleSubmit(handleSubmit)} className="border border-solid border-black space-y-4 max-w-3xl mx-auto py-10 p-4 mt-4">
                <FormField
                    control={form.control}
                    name="customerId"
                    disabled={isSubmit}
                    render={({ field }) => (
                        <>
                        <Selector
                            searchKey="customer"
                            label="ID Customer"
                            value={field.value}
                            fetchItemsFn={fetchCustomerIds}
                            onChange={field.onChange}
                            renderDisplayOnFound={(items) => {
                                if(!field.value) {
                                    return "Cari ID Customer"
                                }

                                const selectedItem = items.find((item) => item.id === field.value);
                                if(!selectedItem) {
                                    return "Cari ID Customer"
                                }

                                return `${selectedItem.id_customer} (${selectedItem.name})`
                            }}
                            renderDropdownList={(customer: CustomerID) => `${customer.id_customer} (${customer.name})`}
                        />
                        <Sheet open={isSheetOpen} onOpenChange={setIsSheetOpen}>
                            <SheetTrigger asChild>
                                <button className="text-blue-500 underline text-sm p-0 leading-none">
                                    ID Customer tidak ditemukan? Tambahkan disini
                                </button>
                            </SheetTrigger>
                            <SheetContent side="bottom">
                                <SheetHeader className="p-4">
                                    <SheetTitle>Masukkan Data Baru</SheetTitle>
                                </SheetHeader>
                                <SheetDescription>
                                    Pastikan sudah melakukan pencarian ID Customer terlebih dahulu sebelum memutuskan untuk mengisi ini.
                                </SheetDescription>
                                <div className="p-4 flex-1">
                                    <Input
                                        placeholder="Masukkan ID Customer"
                                        onChange={(e) => setCustomCustomerId(e.target.value)}
                                        value={customCustomerId}
                                    />
                                    <Button className="mt-4" onClick={() => {
                                        form.setValue("customerId", customCustomerId)
                                        setIsSheetOpen(false)
                                    }}>
                                        Simpan
                                    </Button>
                                </div>

                            </SheetContent>
                        </Sheet>
                        </>
                    )}
                />
                <FormField
                    control={form.control}
                    name="driverId"
                    render={({ field }) => (
                        <Selector
                            searchKey="driver"
                            label="NIK Supir"
                            value={field.value}
                            fetchItemsFn={fetchDrivers}
                            onChange={field.onChange}
                            renderDisplayOnFound={(items) => {
                                if(!field.value) {
                                    return "Cari NIK Supir"
                                }

                                const selectedItem = items.find((item) => item.id === field.value);
                                if(!selectedItem) {
                                    return "Cari NIK Supir"
                                }

                                return `${selectedItem.nik} (${selectedItem.name})`
                            }}
                            renderDropdownList={(driver: Driver) => `${driver.nik} (${driver.name})`}
                        />
                    )}
                />

                {questions.map(question => (
                    <FormField
                        key={question.id}
                        control={form.control}
                        name={`questions.${question.id}`}
                        render={({ field }) => (
                            <FormItem className="flex flex-col items-start space-y-4">
                                <FormLabel>{question.title}</FormLabel>
                                <FormControl className="w-full">
                                    <Rating
                                        size="lg"
                                        value={field.value ?? 0}
                                        onChange={(value) => {
                                            form.setValue(`questions.${question.id}`, value, {
                                                shouldValidate: true,
                                            });
                                        }}
                                    />
                                </FormControl>
                                <FormDescription>Nilai ({field.value ?? 0}/5)</FormDescription>
                                <FormMessage>{form.formState.errors.questions?.[question.id]?.message}</FormMessage>
                                <Separator />
                            </FormItem>
                        )}
                    />
                ))}
                <Button type="submit" className="w-full mb-4" disabled={isSubmit}>Kirim</Button>
                {
                    showLinkToSurveyDecline &&
                    <div className="space-y-2 text-center">
                        <a href="/decline-survey" target="_blank" rel="noopener noreferrer" className="underline text-blue-500">
                            Menu Supir
                        </a>
                    </div>
                }
                </form>
            </Form>
        </div>
    )
}
